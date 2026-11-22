<?php

namespace App\Enums;

enum ProductAvailability: string
{
    case InStock = 'en_existencia';
    case OnDemand = 'bajo_pedido';
    case OutOfStock = 'agotado';

    public function label(): string
    {
        return match ($this) {
            self::InStock => 'En existencia',
            self::OnDemand => 'Bajo pedido',
            self::OutOfStock => 'Agotado',
        };
    }
}
