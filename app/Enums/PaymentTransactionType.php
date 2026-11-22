<?php

namespace App\Enums;

enum PaymentTransactionType: string
{
    case Deposit = 'deposito';
    case Balance = 'restante';
    case Refund = 'reembolso';

    public function label(): string
    {
        return match ($this) {
            self::Deposit => 'Depósito',
            self::Balance => 'Cargo restante',
            self::Refund => 'Reembolso',
        };
    }
}
