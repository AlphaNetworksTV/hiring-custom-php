<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use App\Contracts\Queues\EpgPublisher;
use App\Contracts\Storage\EpgStorage;
use App\Http\Controllers\EpgController;
use Carbon\CarbonImmutable;
use Enalean\Prometheus\PushGateway\Pusher;
use Enalean\Prometheus\Storage\InMemoryStore;
use Exceptions\Http\Client\NotFoundException;
use Illuminate\Validation\ValidationException;
use Laminas\Diactoros\ServerRequestFactory;
use Tests\TestCase;

final class EpgControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        CarbonImmutable::setTestNow(null);
    }

    public function testPost()
    {
        $now = CarbonImmutable::create(2021, 7, 15, 18, 0, 0);
        CarbonImmutable::setTestNow($now);

        $epgPublisher = $this->createMock(EpgPublisher::class);
        $epgPublisher->expects($this->once())->method('publishEpg');

        $json = file_get_contents(base_path('tests/data/epg.json'));

        $epgStorage = $this->createMock(EpgStorage::class);
        $epgStorage->expects($this->once())->method('storeEpg')
            ->with(
                $this->equalTo('plurimedia'),
                $this->equalTo(CarbonImmutable::createFromFormat(DATE_ATOM, '2019-03-09T00:00:00+00:00')),
                $this->equalTo($now),
                $this->equalTo(json_decode($json, true))
            );

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/v1/epgs')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(json_decode($json, true));

        $metricsPusher = $this->createMock(Pusher::class);

        $store = new InMemoryStore();
        $controller = new EpgController($epgPublisher, $epgStorage, $metricsPusher, $store);
        $controller->post($request);
    }

    public function testPostWithEmptyData()
    {
        $this->expectException(ValidationException::class);

        $epgPublisher = $this->createMock(EpgPublisher::class);
        $epgPublisher->expects($this->never())->method('publishEpg');

        $epgStorage = $this->createMock(EpgStorage::class);
        $epgStorage->expects($this->never())->method('storeEpg');

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/v1/epgs')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(null);

        $metricsPusher = $this->createMock(Pusher::class);

        $store = new InMemoryStore();
        $controller = new EpgController($epgPublisher, $epgStorage, $metricsPusher, $store);
        $controller->post($request);
    }

    public function testPostWithErrors()
    {
        $this->expectException(ValidationException::class);

        $epgPublisher = $this->createMock(EpgPublisher::class);
        $epgPublisher->expects($this->never())->method('publishEpg');

        $epgStorage = $this->createMock(EpgStorage::class);
        $epgStorage->expects($this->never())->method('storeEpg');

        $json = file_get_contents(base_path('tests/data/epg_with_errors.json'));

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/v1/epgs')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(json_decode($json, true));

        $metricsPusher = $this->createMock(Pusher::class);

        $store = new InMemoryStore();
        $controller = new EpgController($epgPublisher, $epgStorage, $metricsPusher, $store);
        $controller->post($request);
    }
}
