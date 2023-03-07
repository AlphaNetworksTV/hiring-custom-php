<?php

declare(strict_types=1);

namespace App\Queues;

use App\Contracts\Queues\EpgPublisher as Contract;

final class EpgPublisher implements Contract
{
    /**
     * @inheritdoc
     */
    public function publishEpg(array $epg): void
    {
        // TODO
    }
}
