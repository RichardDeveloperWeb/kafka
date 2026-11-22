<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Confirmed = 'confirmado';
    case Preparing = 'preparando';
    case Shipped = 'enviado';
    case Delivered = 'entregado';
    case Cancelled = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::Confirmed => 'Confirmado',
            self::Preparing => 'Preparando',
            self::Shipped => 'Enviado',
            self::Delivered => 'Entregado',
            self::Cancelled => 'Cancelado',
        };
    }
}
