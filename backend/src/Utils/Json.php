<?php


namespace App\Utils;


class Json
{
    public static function decode(?string $json): array
    {
        if ($json === null) {
            return [];
        }

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}