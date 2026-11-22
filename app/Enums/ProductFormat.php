<?php

namespace App\Enums;

enum ProductFormat: string
{
    case Printed = 'impreso';
    case Ebook = 'ebook';
    case Audiobook = 'audiolibro';

    public function label(): string
    {
        return match ($this) {
            self::Printed => 'Impreso',
            self::Ebook => 'E-book',
            self::Audiobook => 'Audiolibro',
        };
    }
}
