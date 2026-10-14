<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\Vacancy\ValueObject\ExternalId;

/**
 * @extends StringValueObjectType<ExternalId>
 */
final class ExternalIdType extends StringValueObjectType
{
    public const string NAME = 'external_id';

    protected function valueObjectClass(): string
    {
        return ExternalId::class;
    }
}
