<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Source\Message\Command;

use App\VacancyGrabber\Application\Component\Source\MessageHandler\Command\PatchSourceHandler;
use Symfony\Component\Uid\Uuid;

/** @see PatchSourceHandler */
final readonly class PatchSourceCommand
{
    public function __construct(
        public Uuid $userId,
        public Uuid $sourceId,
        public array $properties,
        public ?string $name,
        public ?string $url,
        public ?bool $active,
    ) {
    }
}
