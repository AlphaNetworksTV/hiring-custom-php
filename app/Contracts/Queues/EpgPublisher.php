<?php

declare(strict_types=1);

namespace App\Contracts\Queues;

interface EpgPublisher
{
    public function publishEpg(array $epg): void;
}
