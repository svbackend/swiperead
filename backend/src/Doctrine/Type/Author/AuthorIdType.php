<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Author;

use App\ValueObject\Author\AuthorId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class AuthorIdType extends GuidType
{
    public const NAME = 'author_id';

    /** @param $value AuthorId|string */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof AuthorId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?AuthorId
    {
        return !empty($value) ? new AuthorId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}