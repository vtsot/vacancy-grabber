<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use App\Domain\Shared\EntityTrait\TimeStampableEntityTrait;
use Symfony\Component\Uid\Uuid;

abstract class AbstractEntity implements EntityInterface
{
    use TimeStampableEntityTrait;

    protected ?Uuid $id = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }
}
