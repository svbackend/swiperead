<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Book;

use App\ValueObject\Book\BookId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class BookIdType extends GuidType
{
    public const NAME = 'book_id';

    /** @param $value BookId|string */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof BookId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?BookId
    {
        return !empty($value) ? new BookId((string)$value) : null;
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