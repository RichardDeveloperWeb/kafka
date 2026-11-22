<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Deposited = 'depositado';
    case Charged = 'cobrado';
    case Cancelled = 'cancelado';
    case Refunded = 'reembolsado';

    public function label(): string
    {
        return match ($this) {
            self::Deposited => 'Depositado',
            self::Charged => 'Cobrado',
            self::Cancelled => 'Cancelado',
            self::Refunded => 'Reembolsado',
        };
    }
}
