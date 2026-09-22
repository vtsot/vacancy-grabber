<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Command;

use App\Component\Vacancy\MessageHandler\Command\CreateVacancyHandler;

/** @see CreateVacancyHandler */
final readonly class CreateVacancyCommand
{
    public function __construct(
        public ?string $name,
        public ?string $url,
        public ?bool $active,
    ) {
    }
}
