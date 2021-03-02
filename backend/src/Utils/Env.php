<?php

namespace App\Utils;

use JetBrains\PhpStorm\Pure;

class Env
{
    #[Pure]
    public static function get(string $var): null|false|string
    {
        return $_ENV[$var] ?? getenv($var);
    }
}