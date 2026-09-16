<?php

declare(strict_types=1);

namespace App\Component\Common\Shared\EntityTrait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait ViewedEntityTrait
{
    #[ORM\Column(name: 'viewed', type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    private bool $viewed = false;

    public function isViewed(): bool
    {
        return $this->viewed;
    }

    public function setViewed(bool $viewed): static
    {
        $this->viewed = $viewed;

        return $this;
    }
}
