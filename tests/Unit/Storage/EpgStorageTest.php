<?php

declare(strict_types=1);

namespace Tests\Unit\Storage;

use App\Storage\EpgStorage;
use Carbon\CarbonImmutable;
use League\Flysystem\FilesystemWriter;
use Tests\TestCase;

final class EpgStorageTest extends TestCase
{
    public function test_storeEpg()
    {
        $writer = $this->createMock(FilesystemWriter::class);

        $valueDate = CarbonImmutable::create(2021,7,15,1,2,3);
        $receivedAt = CarbonImmutable::create(2021,7,15,18,7,0);

        $writer->expects($this->once())
            ->method('write')
            ->with(
                $this->equalTo('epg-api/test/2021/07/15/20210715_20210715180700.json.gz'),
                $this->callback(function ($value) {
                    // $value is gzdlated json content
                    // str below = bin2hex(gzencode(json_encode(['some' => 'data'])))

                    return '1f8b0800000000000203ab562acecf4d55b2524a492c4954aa0500113756210f000000' === bin2hex($value);
                })
            );

        $sut = new EpgStorage($writer, 'epg-api');
        $sut->storeEpg( 'test', $valueDate, $receivedAt, ['some' => 'data']);
    }
}
