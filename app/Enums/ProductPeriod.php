<?php

namespace App\Enums;

enum ProductPeriod: string
{
    case monthly = 'monthly';
    case yearly = 'yearly';

    public static function values(): array
    {
        return array_map(static function (self $enum) {
            return $enum->value;
        }, self::cases());
    }
}
