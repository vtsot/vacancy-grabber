<?php

declare(strict_types=1);

namespace App\Entity;

use App\Contract\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

abstract class AbstractEntity implements EntityInterface
{
    #[ORM\Id()]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    protected ?Uuid $id = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }
}
