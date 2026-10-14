<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Source\Message\Command;

use App\VacancyGrabber\Application\Component\Source\MessageHandler\Command\DeleteSourceHandler;
use Symfony\Component\Uid\Uuid;

/** @see DeleteSourceHandler */
final readonly class DeleteSourceCommand
{
    public function __construct(
        public Uuid $userId,
        public Uuid $sourceId,
    ) {
    }
}
