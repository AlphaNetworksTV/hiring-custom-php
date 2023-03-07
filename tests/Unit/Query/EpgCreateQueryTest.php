<?php

declare(strict_types=1);

namespace Tests\Unit\Query;

use App\Query\EpgCreateQuery;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class EpgCreateQueryTest extends TestCase
{
    /**
     * @dataProvider requestsProvider
     *
     * @param array $data
     * @param string $expectedException
     * @param null $expected
     * @throws ValidationException
     */
    public function testCreateFromRequest($data, $expectedException, $expected = null)
    {
        if ($expectedException) {
            $this->expectException(ValidationException::class);
        }

        if (empty($expected)) {
            $expected = $data;
        }

        $query = EpgCreateQuery::fromRequest($data);
        $result = $query->getEpg();
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($result));
    }

    public function requestsProvider()
    {
        $input = json_decode(file_get_contents(base_path('tests/data/epg.json')), true);

        yield '✓ all ok' => [$input, false];

        yield '✓ additional data' => [
            collect($input)->merge(['something' => 'else'])->toArray(),
            false,
            $input,
        ];

        yield '✗ no data' => [[], true];

        // channel_id

        yield '✓ channel_id: as string' => [
            collect($input)->merge(['channel_id' => '42'])->toArray(),
            false,
        ];

        yield '✗ channel_id: missing' => [
            collect($input)->except(['channel_id'])->toArray(),
            true,
        ];

        yield '✗ channel_id: wrong type' => [
            collect($input)->merge(['channel_id' => 'whatever'])->toArray(),
            true,
        ];

        yield '✗ channel_id: empty' => [
            collect($input)->merge(['channel_id' => ''])->toArray(),
            true,
        ];

        yield '✗ channel_id: null' => [
            collect($input)->merge(['channel_id' => null])->toArray(),
            true,
        ];

        // start_date

        yield '✗ start_date: missing' => [
            collect($input)->except(['start_date'])->toArray(),
            true,
        ];

        yield '✗ start_date: empty' => [
            collect($input)->merge(['start_date' => ''])->toArray(),
            true,
        ];

        yield '✗ start_date: null' => [
            collect($input)->merge(['start_date' => null])->toArray(),
            true,
        ];

        yield '✗ start_date: wrong format' => [
            collect($input)->merge(['start_date' => '2018/03/06'])->toArray(),
            true,
        ];

        yield '✗ start_date: wrong type' => [
            collect($input)->merge(['start_date' => 42])->toArray(),
            true,
        ];

        // broadcasts

        yield '✗ broadcasts: missing' => [
            collect($input)->except(['broadcasts'])->toArray(),
            true,
        ];

        yield '✗ broadcasts: empty' => [
            collect($input)->merge(['broadcasts' => []])->toArray(),
            true,
        ];

        yield '✗ broadcasts: null' => [
            collect($input)->merge(['broadcasts' => null])->toArray(),
            true,
        ];

        // broadcasts.*.id

        yield '✓ broadcasts.*.id: as string' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['id' => '42'])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✗ broadcasts.*.id: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['id'])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.id: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['id' => 'whatever'])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.id: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['id' => ''])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.id: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['id' => null])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.airing_start

        yield '✗ broadcasts.*.airing_start: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['airing_start'])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.airing_start: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['airing_start' => ''])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.airing_start: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['airing_start' => null])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.airing_start: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['airing_start' => 'whatever'])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.airing_start: wrong format' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['airing_start' => '2018/03/06'])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.airing_end

        yield '✗ broadcasts.*.airing_end: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['airing_end'])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.airing_end: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['airing_end' => ''])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.airing_end: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['airing_end' => null])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.airing_end: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['airing_end' => 'whatever'])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.airing_end: wrong format' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['airing_end' => '2018/03/06'])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.title

        yield '✗ broadcasts.*.title: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['title'])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.title: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['title' => ''])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.title: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['title' => null])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.title: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['title' => 42])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.sub_title

        yield '✓ broadcasts.*.sub_title: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['sub_title'])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.sub_title: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['sub_title' => ''])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.sub_title: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['sub_title' => null])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✗ broadcasts.*.sub_title: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['sub_title' => 42])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.multilingual

        yield '✓ broadcasts.*.multilingual: empty (/!\)' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['multilingual' => ' '])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.multilingual: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['multilingual' => null])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.multilingual: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['multilingual'])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✗ broadcasts.*.multilingual: wrong type (str)' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['multilingual' => 'true'])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✓ broadcasts.*.multilingual: as int' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['multilingual' => 0])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.multilingual: as integerish' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['multilingual' => '0'])->toArray(),
            ]])->toArray(),
            false,
        ];

        // broadcasts.*.parental_rating

        yield '✓ broadcasts.*.parental_rating: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['parental_rating' => ''])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.parental_rating: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['parental_rating' => null])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.parental_rating: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['parental_rating'])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✗ broadcasts.*.parental_rating: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['parental_rating' => 1])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.synopsis

        yield '✓ broadcasts.*.synopsis: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['synopsis' => ''])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.synopsis: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['synopsis' => null])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.synopsis: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['synopsis'])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✗ broadcasts.*.synopsis: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['synopsis' => 1])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.audio_format

        yield '✓ broadcasts.*.audio_format: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['audio_format' => ''])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.audio_format: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['audio_format' => null])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.audio_format: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['audio_format'])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✗ broadcasts.*.audio_format: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['audio_format' => 1])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.category

        yield '✓ broadcasts.*.category: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['category' => ''])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.category: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['category' => null])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.category: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['category'])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✗ broadcasts.*.category: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['category' => 1])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.sub_category

        yield '✓ broadcasts.*.sub_category: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['sub_category' => ''])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.sub_category: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['sub_category' => null])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✓ broadcasts.*.sub_category: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['sub_category'])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✗ broadcasts.*.sub_category: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['sub_category' => 1])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.provider

        yield '✗ broadcasts.*.provider: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['provider'])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' => []])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' => null])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.provider.name

        yield '✗ broadcasts.*.provider.name: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->except(['name'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider.name: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->merge(['name' => 42])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider.name: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->merge(['name' => ''])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider.name: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->merge(['name' => null])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.provider.last_update

        yield '✗ broadcasts.*.provider.last_update: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->except(['last_update'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider.last_update: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->merge(['last_update' => 42])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider.last_update: wrong format' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->merge(['last_update' => '2018/03/06'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider.last_update: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->merge(['last_update' => ''])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider.last_update: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->merge(['last_update' => null])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.provider.broadcast_id

        yield '✗ broadcasts.*.provider.broadcast_id: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->except(['broadcast_id'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider.broadcast_id: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->merge(['broadcast_id' => 42])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider.broadcast_id: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->merge(['broadcast_id' => ''])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.provider.broadcast_id: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['provider' =>
                    collect($input['broadcasts'][0]['provider'])->merge(['broadcast_id' => null])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.asset

        yield '✗ broadcasts.*.asset: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->except(['asset'])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' => []])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' => null])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.asset.id

        yield '✗ broadcasts.*.asset.id: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->except(['id'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✓ broadcasts.*.asset.id: as string' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['id' => '42'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✗ broadcasts.*.asset.id: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['id' => 'whatever'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset.id: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['id' => ''])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset.id: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['id' => null])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.asset.last_update

        yield '✗ broadcasts.*.asset.last_update: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->except(['last_update'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset.last_update: wrong type' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['last_update' => 42])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset.last_update: wrong format' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['last_update' => '2018/03/06'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset.last_update: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['last_update' => ''])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset.last_update: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['last_update' => null])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        // broadcasts.*.asset.duration

        yield '✗ broadcasts.*.asset.duration: missing' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->except(['duration'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset.duration: empty' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['duration' => ''])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset.duration: null' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['duration' => null])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✓ broadcasts.*.asset.duration: as string' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['duration' => '42'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            false,
        ];

        yield '✗ broadcasts.*.asset.duration: as string wrong format' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['duration' => 'whatever'])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];

        yield '✗ broadcasts.*.asset.duration: negative' => [
            collect($input)->merge(['broadcasts' => [
                collect($input['broadcasts'][0])->merge(['asset' =>
                    collect($input['broadcasts'][0]['asset'])->merge(['duration' => -42])->toArray(),
                ])->toArray(),
            ]])->toArray(),
            true,
        ];
    }
}
