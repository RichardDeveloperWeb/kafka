<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Active = 'activa';
    case Used = 'consumida';
    case Expired = 'expirada';
    case Cancelled = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activa',
            self::Used => 'Consumida',
            self::Expired => 'Expirada',
            self::Cancelled => 'Cancelada',
        };
    }
}
