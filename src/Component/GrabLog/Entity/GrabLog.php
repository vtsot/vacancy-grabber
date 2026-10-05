<?php

declare(strict_types=1);

namespace App\Component\GrabLog\Entity;

use App\Component\General\Shared\AbstractEntity;
use App\Component\General\Shared\EntityTrait\DescriptionEntityTrait;
use App\Component\GrabLog\Repository\GrabLogRepository;
use App\Component\Source\Entity\Source;
use App\Component\User\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function array_filter;
use function implode;

#[ORM\Table(name: 'grab_logs')]
#[ORM\Index(name: 'fk_user_id', columns: ['user_id'])]
#[ORM\Index(name: 'fk_source_id', columns: ['source_id'])]
#[ORM\Entity(repositoryClass: GrabLogRepository::class)]
class GrabLog extends AbstractEntity
{
    use DescriptionEntityTrait;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Source::class, inversedBy: 'grabLogs')]
    #[ORM\JoinColumn(name: 'source_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Source $source = null;

    #[ORM\Column(name: 'counter_company_created', type: Types::INTEGER, nullable: false, options: ['default' => 0])]
    private int $counterCompanyCreated = 0;

    #[ORM\Column(name: 'counter_vacancy_created', type: Types::INTEGER, nullable: false, options: ['default' => 0])]
    private int $counterVacancyCreated = 0;

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = 'Grab Log ' . $this->getCreatedAt()->format('d-m-Y-H-i-s') ?? 'New Entity';

        return implode(' ', array_filter($labels));
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getCounterCompanyCreated(): ?int
    {
        return $this->counterCompanyCreated;
    }

    public function setCounterCompanyCreated(int $counterCompanyCreated): static
    {
        $this->counterCompanyCreated = $counterCompanyCreated;

        return $this;
    }

    public function getCounterVacancyCreated(): ?int
    {
        return $this->counterVacancyCreated;
    }

    public function setCounterVacancyCreated(int $counterVacancyCreated): static
    {
        $this->counterVacancyCreated = $counterVacancyCreated;

        return $this;
    }
}
