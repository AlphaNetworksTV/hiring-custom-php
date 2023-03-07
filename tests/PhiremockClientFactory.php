<?php

declare(strict_types=1);

namespace Tests;

use Http\Discovery\Psr18ClientDiscovery;
use Mcustiel\Phiremock\Client\Factory;
use Psr\Http\Client\ClientInterface;

final class PhiremockClientFactory extends Factory
{
    public function createRemoteConnection(): ClientInterface
    {
        return Psr18ClientDiscovery::find();
    }
}
