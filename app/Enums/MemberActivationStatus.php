<?php

namespace App\Enums;

enum MemberActivationStatus: int
{
    case PENDING = 1;
    case VERIFIED = 2;
    case REJECTED = 3;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::VERIFIED => 'Terverifikasi',
            self::REJECTED => 'Ditolak',
        };
    }
}
