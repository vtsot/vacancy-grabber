<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\StringType;
use Stringable;

use function is_string;

/**
 * Stores a string value object in a plain VARCHAR column.
 *
 * @template T of Stringable
 */
abstract class StringValueObjectType extends StringType
{
    /**
     * @return class-string<T>
     */
    abstract protected function valueObjectClass(): string;

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Stringable
    {
        if ($value === null || $value instanceof Stringable) {
            return $value;
        }

        return $this->valueObjectClass()::fromStorage((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null || is_string($value)) {
            return $value;
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        throw InvalidType::new($value, static::class, ['null', 'string', $this->valueObjectClass()]);
    }
}
