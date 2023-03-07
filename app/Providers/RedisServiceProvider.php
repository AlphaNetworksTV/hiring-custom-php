<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Redis\Factory;
use Illuminate\Redis\RedisServiceProvider as IlluminateRedisServiceProvider;
use Illuminate\Support\ServiceProvider;

final class RedisServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(IlluminateRedisServiceProvider::class);

        $this->app->bind(
            Factory::class,
            fn(Container $app) => $app->make('redis')
        );
    }
}
