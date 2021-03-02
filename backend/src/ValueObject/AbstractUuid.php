<?php

declare(strict_types=1);

namespace App\ValueObject;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

abstract class AbstractUuid
{
    public const NIL_UUID = '00000000-0000-0000-0000-000000000000';

    private string $value;

    public function __construct(string $value)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
    }

    public static function generate(): static
    {
        return new static(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}