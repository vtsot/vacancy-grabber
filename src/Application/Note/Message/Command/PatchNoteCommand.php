<?php

declare(strict_types=1);

namespace App\Application\Note\Message\Command;

use App\Application\Note\MessageHandler\Command\PatchNoteHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see PatchNoteHandler */
final readonly class PatchNoteCommand
{
    public function __construct(
        public ?Uuid $userId,
        public ?Uuid $noteId,
        public array $properties,
        // fields
        public ?CarbonImmutable $notedAt = null,
        public ?string $description = null,
    ) {
    }
}
