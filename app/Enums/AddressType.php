<?php

namespace App\Enums;

enum AddressType: string
{
    case Shipping = 'envio';
    case Billing = 'facturacion';

    public function label(): string
    {
        return match ($this) {
            self::Shipping => 'Envío',
            self::Billing => 'Facturación',
        };
    }
}
