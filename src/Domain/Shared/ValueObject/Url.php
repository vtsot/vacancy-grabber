<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;
use Stringable;

use function filter_var;
use function mb_strlen;
use function sprintf;
use function trim;

use const FILTER_VALIDATE_URL;

final readonly class Url implements Stringable
{
    private const int MAX_LENGTH = 255;

    private function __construct(
        private string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if (filter_var($value, FILTER_VALIDATE_URL) === false || mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid URL.', $value));
        }

        return new self($value);
    }

    /**
     * Empty input means "no URL".
     */
    public static function fromNullable(?string $value): ?self
    {
        return $value === null || trim($value) === '' ? null : self::fromString($value);
    }

    /**
     * Rebuilds a stored value without re-validating it (rows written before validation existed).
     *
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
