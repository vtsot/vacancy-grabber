<?php

declare(strict_types=1);

namespace App\Component\General\Shared;

use App\Component\General\Shared\EntityTrait\TimeStampableEntityTrait;
use App\Contract\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\MappedSuperclass]
#[ORM\Index(name: 'created_at_idx', columns: ['created_at'])]
#[ORM\Index(name: 'updated_at_idx', columns: ['updated_at'])]
abstract class AbstractEntity implements EntityInterface
{
    use TimeStampableEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    protected ?Uuid $id = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }
}
