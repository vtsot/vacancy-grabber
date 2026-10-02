<?php

declare(strict_types=1);

namespace App\Component\Source\Message\Command;

use App\Component\Source\MessageHandler\Command\CreateSourceHandler;
use Symfony\Component\Uid\Uuid;

/** @see CreateSourceHandler */
final readonly class CreateSourceCommand
{
    public function __construct(
        public Uuid $userId,
        public ?string $name,
        public ?string $url,
        public ?bool $active,
    ) {
    }
}
