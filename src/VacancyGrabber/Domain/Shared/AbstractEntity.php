<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared;

use App\VacancyGrabber\Domain\Shared\EntityTrait\TimeStampableEntityTrait;
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
