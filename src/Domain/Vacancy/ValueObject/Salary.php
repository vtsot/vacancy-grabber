<?php

declare(strict_types=1);

namespace App\Domain\Vacancy\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;
use Stringable;

use function mb_strlen;
use function preg_replace;
use function sprintf;
use function trim;

/**
 * Free-text salary as published by the source (e.g. "$2000-3000"), whitespace-normalised.
 */
final readonly class Salary implements Stringable
{
    private const int MAX_LENGTH = 255;

    private function __construct(
        private string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

        if ($value === '' || mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid salary.', $value));
        }

        return new self($value);
    }

    /**
     * Empty input means "not set".
     */
    public static function fromNullable(?string $value): ?self
    {
        return $value === null || trim($value) === '' ? null : self::fromString($value);
    }

    /**
     * @internal persistence only
     */
    public static function fromStorage(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
