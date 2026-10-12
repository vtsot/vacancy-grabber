<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\Entity;

use App\Contract\Entity\EntityInterface;
use App\VacancyGrabber\Domain\Shared\Entity\Traits\TimeStampableEntityTrait;
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
