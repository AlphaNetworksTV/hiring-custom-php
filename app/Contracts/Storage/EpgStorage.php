<?php

declare(strict_types=1);

namespace App\Contracts\Storage;

use DateTimeInterface;

interface EpgStorage
{
    public function storeEpg(
        string $providerName,
        DateTimeInterface $valueDate,
        DateTimeInterface $receivedAt,
        array $epg
    ): void;
}
