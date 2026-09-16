<?php

declare(strict_types=1);

namespace App\Component\Source\Message\Command;

use App\Component\Source\MessageHandler\Command\CreateSourceHandler;

/** @see CreateSourceHandler */
final readonly class CreateSourceCommand
{
    public function __construct(
        public ?string $name,
        public ?string $url,
        public ?bool $active,
    ) {
    }
}
