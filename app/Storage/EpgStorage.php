<?php

declare(strict_types=1);

namespace App\Storage;

use App\Contracts\Storage\EpgStorage as Contract;
use DateTimeInterface;
use League\Flysystem\FilesystemWriter;

final class EpgStorage implements Contract
{
    private FilesystemWriter $filesystemWriter;
    private string $folder;

    public function __construct(FilesystemWriter $filesystemWriter, string $folder)
    {
        $this->filesystemWriter = $filesystemWriter;
        $this->folder = $folder;
    }

    public function storeEpg(
        string $providerName,
        DateTimeInterface $valueDate,
        DateTimeInterface $receivedAt,
        array $epg
    ): void {
        $filename = "{$valueDate->format('Ymd')}_{$receivedAt->format('Ymdhis')}.json.gz";
        $path = "$this->folder/$providerName/{$valueDate->format('Y/m/d')}/$filename";

        // compress as gzip ; diff between compression method can be found at
        // https://gist.github.com/fruitl00p/86321d00aef0e79c1c2b9a6340525eaa
        //
        // epg-fetcher (go) and env-synchronizer (php) expects "gzencode" method
        $this->filesystemWriter->write($path, gzencode(json_encode($epg), 9));
    }
}
