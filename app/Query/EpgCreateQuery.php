<?php

declare(strict_types=1);

namespace App\Query;

use Illuminate\Validation\ValidationException;

final class EpgCreateQuery
{
    private array $epg;

    private function __construct(array $rawData)
    {
        $this->epg = $rawData;
    }

    /**
     * @throws ValidationException
     */
    public static function fromRequest(array $data): self
    {
        $validator = validator($data, [
            'channel_id' => 'required|integer',
            'start_date' => 'required|date_format:' . DATE_ATOM,
            'broadcasts' => 'required|filled',
            'broadcasts.*.id' => 'required|integer',
            'broadcasts.*.airing_start' => 'required|date_format:' . DATE_ATOM,
            'broadcasts.*.airing_end' => 'required|date_format:' . DATE_ATOM,
            'broadcasts.*.title' => 'required|string',
            'broadcasts.*.sub_title' => 'nullable|string',
            'broadcasts.*.multilingual' => 'nullable|boolean',
            'broadcasts.*.parental_rating' => 'nullable|string',
            'broadcasts.*.synopsis' => 'nullable|string',
            'broadcasts.*.audio_format' => 'nullable|string',
            'broadcasts.*.category' => 'nullable|string',
            'broadcasts.*.sub_category' => 'nullable|string',
            'broadcasts.*.provider' => 'required',
            'broadcasts.*.provider.name' => 'required|string',
            'broadcasts.*.provider.broadcast_id' => 'required|string',
            'broadcasts.*.provider.last_update' => 'required|date_format:' . DATE_ATOM,
            'broadcasts.*.asset' => 'required',
            'broadcasts.*.asset.id' => 'required|integer',
            'broadcasts.*.asset.last_update' => 'required|date_format:' . DATE_ATOM,
            'broadcasts.*.asset.duration' => 'required|integer|min:0',
        ]);

        return new self($validator->validate());
    }

    /**
     * @return array
     */
    public function getEpg(): array
    {
        return $this->epg;
    }
}
