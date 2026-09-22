<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Command;

use App\Component\Vacancy\MessageHandler\Command\DeleteVacancyHandler;
use Symfony\Component\Uid\Uuid;

/** @see DeleteVacancyHandler */
final readonly class DeleteVacancyCommand
{
    public function __construct(
        public Uuid $id,
    ) {
    }
}
