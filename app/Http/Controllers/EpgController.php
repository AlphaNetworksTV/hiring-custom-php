<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Assert\Assert;
use App\Contracts\Queues\EpgPublisher;
use App\Contracts\Storage\EpgStorage;
use App\Query\EpgCreateQuery;
use Carbon\CarbonImmutable;
use Enalean\Prometheus\PushGateway\Pusher;
use Enalean\Prometheus\Registry\CollectorRegistry;
use Enalean\Prometheus\Storage\Store;
use Enalean\Prometheus\Value\MetricLabelNames;
use Enalean\Prometheus\Value\MetricName;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller;
use Psr\Http\Message\ServerRequestInterface;
use SebastianBergmann\Timer\Timer;
use Symfony\Component\HttpFoundation\Response;
use function app;
use function current;
use function reset;
use const DATE_ATOM;

final class EpgController extends Controller
{
    private EpgPublisher $epgPublisher;
    private EpgStorage $epgStorage;
    private Pusher $pusher;
    private Store $prometheusStore;

    private array $pushMeta;
    private string $pushJob;

    public function __construct(
        EpgPublisher    $epgPublisher,
        EpgStorage      $epgStorage,
        Pusher          $pusher,
        Store           $prometheusStore
    )
    {
        $this->epgPublisher = $epgPublisher;
        $this->epgStorage = $epgStorage;
        $this->pusher = $pusher;

        $config = app('config');

        $this->pushJob = $config->get('app.name');
        $this->pushMeta = [
            'instance' => gethostname(),
            'version' => $config->get('aepg.version'),
            'env' => $config->get('app.env')
        ];
        $this->prometheusStore = $prometheusStore;
    }

    /**
     * @throws ValidationException
     */
    public function post(ServerRequestInterface $request): JsonResponse
    {
        $registry = new CollectorRegistry($this->prometheusStore);

        try {
            return $this->doPost($request, $registry);
        } finally {
            $this->pusher->push($registry, $this->pushJob, $this->pushMeta);
        }
    }

    /**
     * @throws ValidationException
     */
    private function doPost(ServerRequestInterface $request, CollectorRegistry $registry): JsonResponse
    {
        $timer = new Timer();

        $env = $this->pushMeta['env'];

        $gauge = $registry->getOrRegisterGauge(
            MetricName::fromNamespacedName('aepg', 'epg_api_internal_execution_time_us'),
            'EPG API Internal Execution time',
            MetricLabelNames::fromNames('env', 'action'),
        );

        $now = CarbonImmutable::now();

        Assert::true(
            Str::contains($request->getHeaderLine('Content-Type'), ['/json', '+json']),
            'Expected json Content-Type'
        );

        $query = EpgCreateQuery::fromRequest((array)$request->getParsedBody());

        $epg = $query->getEpg();

        $timer->start();

        // We assume that one EPG is provided by one provider,
        //even if the spec allow us to have multiple providers in same EPG

        $broadcasts = $epg['broadcasts'];
        reset($broadcasts);
        $firstBroadcast = current($broadcasts);
        $providerName = $firstBroadcast['provider']['name'];

        $startDate = CarbonImmutable::createFromFormat(DATE_ATOM, $epg['start_date']);

        $timer->start();
        try {
            $this->epgPublisher->publishEpg($epg);
        } finally {
            $d = $timer->stop();
            $gauge->set($d->asMicroseconds(), $env, 'rmq_publish_epg');
        }

        $timer->start();
        try {
            $this->epgStorage->storeEpg($providerName, $startDate, $now, $epg);
        } finally {
            $d = $timer->stop();
            $gauge->set($d->asMicroseconds(), $env, 'store_epg');
        }

        return JsonResponse::create(null, Response::HTTP_ACCEPTED);
    }
}
