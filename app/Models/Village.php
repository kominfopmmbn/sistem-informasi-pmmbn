<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravolt\Indonesia\Models\Village as BaseModel;

class Village extends BaseModel
{
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }

    public function getNameWithCodeAttribute(): string
    {
        return $this->name . ' (code: ' . $this->code . ')';
    }

    /** Kode pos disimpan di dalam kolom `meta` JSON (key `pos`) oleh data Laravolt, bukan kolom tersendiri. */
    public function getPostalCodeAttribute(): ?string
    {
        $meta = $this->meta;
        if (is_string($meta)) {
            $meta = json_decode($meta, true);
        }

        return is_array($meta) && isset($meta['pos']) && $meta['pos'] !== ''
            ? (string) $meta['pos']
            : null;
    }

    /** Label opsi select: "{kode pos} - {kelurahan} - {kecamatan} - {kabupaten} - {provinsi}"; butuh relasi district.city.province ter-load. */
    public function getSelectLabelAttribute(): string
    {
        $parts = array_filter([
            $this->postal_code,
            $this->name,
            $this->district?->name,
            $this->district?->city?->name,
            $this->district?->city?->province?->name,
        ], static fn ($part) => $part !== null && $part !== '');

        return implode(' - ', $parts);
    }
}
