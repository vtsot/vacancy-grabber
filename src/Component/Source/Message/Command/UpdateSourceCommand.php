<?php

declare(strict_types=1);

namespace App\Component\Source\Message\Command;

use App\Component\Source\MessageHandler\Command\UpdateSourceHandler;
use Symfony\Component\Uid\Uuid;

/** @see UpdateSourceHandler */
final readonly class UpdateSourceCommand
{
    public function __construct(
        public Uuid $userId,
        public Uuid $sourceId,
        public ?string $name,
        public ?string $url,
        public ?bool $active,
    ) {
    }
}
