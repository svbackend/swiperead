<?php

namespace App\Utils;

class CastHelper
{
    public static function toInt($val): ?int
    {
        if (is_int($val)) {
            return $val;
        }

        if ($val === null) {
            return null;
        }

        if (is_string($val) && trim($val) === '') {
            return null;
        }

        return (int)$val;
    }

    /** In this case Decimal = float or int represented as a string, because Doctrine stores decimals as strings */
    public static function toDecimal($val): ?string
    {
        return self::toStr($val);
    }

    public static function toStr($val): ?string
    {
        if ($val === null || $val === 'null' || (is_string($val) && trim($val) === '')) {
            return null;
        }

        return (string)$val;
    }

    public static function toBool($val): bool
    {
        if (is_bool($val)) {
            return $val;
        }

        if (is_numeric($val)) {
            return $val > 0;
        }

        return $val === 'true';
    }
}