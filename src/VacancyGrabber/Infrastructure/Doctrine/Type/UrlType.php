<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Doctrine\Type;

use App\VacancyGrabber\Domain\Shared\ValueObject\Url;

/**
 * @extends StringValueObjectType<Url>
 */
final class UrlType extends StringValueObjectType
{
    public const string NAME = 'url';

    protected function valueObjectClass(): string
    {
        return Url::class;
    }
}
