<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\Exception;

final class EntityNotFoundException extends DomainException
{
    public function __construct(string $message = 'GENERAL.NOT_FOUND')
    {
        parent::__construct($message);
    }
}
