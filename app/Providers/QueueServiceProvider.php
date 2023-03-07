<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Queues\EpgPublisher as EpgPublisherContract;
use App\Queues\EpgPublisher;
use Illuminate\Support\ServiceProvider;

final class QueueServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            EpgPublisherContract::class,
            fn() => new EpgPublisher()
        );
    }
}
