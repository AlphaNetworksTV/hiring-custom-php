<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Managers\ChannelManager as ChannelManagerContract;
use App\Contracts\Storage\EpgStorage as EpgStorageContract;
use App\Http\Controllers\EpgController;
use App\Managers\Api\ChannelApi;
use App\Storage\EpgStorage;
use Aws\S3\S3Client;
use Enalean\Prometheus\PushGateway\PSR18Pusher;
use Enalean\Prometheus\PushGateway\Pusher;
use Enalean\Prometheus\Storage\RedisStore;
use Enalean\Prometheus\Storage\Store;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\DecoderPlugin;
use Http\Client\Common\Plugin\HeaderSetPlugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemWriter;
use League\Uri\Http;
use function var_dump;

final class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Resource::withoutWrapping();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /** @var AuthManager $auth */
        $auth = $this->app['auth'];

        $auth->viaRequest(
            'api',
            fn(Request $request) => new GenericUser(
                [
                    'id' => preg_replace('/^User /', '', $request->header('Authorization')),
                ]
            )
        );

        $this->app->bind(
            'epg_storage',
            function () {
                $config = $this->app->make(ConfigContract::class);
                return new Filesystem(
                    new AwsS3V3Adapter(
                        new S3Client(
                            [
                                'credentials' => [
                                    'key' => $config->get('aepg.epg_storage_key', ''),
                                    'secret' => $config->get('aepg.epg_storage_password', ''),
                                ],
                                'region' => $config->get('aepg.epg_storage_region', 'eu-west-1'),
                                'version' => 'latest',
                                'use_path_style_endpoint' => true,
                                'endpoint' => $config->get('aepg.epg_storage_url'),
                            ]
                        ),
                        $config->get('aepg.epg_storage_bucket')
                    )
                );
            }
        );

        $this->app->bind(EpgStorageContract::class, EpgStorage::class);

        $this->app->when(EpgStorage::class)
            ->needs(FilesystemWriter::class)
            ->give('epg_storage');

        $this->app->when(EpgStorage::class)
            ->needs('$folder')
            ->give($this->app->make(ConfigContract::class)->get('aepg.epg_storage_folder'));

        $this->app->when(EpgController::class)
            ->needs(Store::class)
            ->give(function () {
                return new RedisStore($this->app->make('redis')->client());
            });


        $this->app->bind(Pusher::class, function () {
            $config = $this->app->make(ConfigContract::class);
            return new PSR18Pusher(
                $config->get('aepg.metrics_push_gateway'),
                Psr18ClientDiscovery::find(),
                Psr17FactoryDiscovery::findRequestFactory(),
                Psr17FactoryDiscovery::findStreamFactory()
            );
        });
    }
}
