<?php

declare(strict_types=1);

namespace App\Component\Source\Entity;

use App\Component\Source\Repository\SourceRepository;
use App\Contract\Dictionary\VacancyGrabber;
use App\Entity\AbstractEntity;
use App\Entity\GrabLog;
use App\Entity\Traits\ActiveEntityTrait;
use App\Entity\Traits\NameEntityTrait;
use App\Entity\Traits\UrlEntityTrait;
use App\Entity\Vacancy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function array_filter;
use function implode;

#[ORM\Table(name: 'sources')]
#[ORM\Index(name: 'idx_active', columns: ['active'])]
#[ORM\Entity(repositoryClass: SourceRepository::class)]
class Source extends AbstractEntity
{
    use NameEntityTrait;
    use UrlEntityTrait;
    use ActiveEntityTrait;

    #[ORM\Column(name: 'grabber', type: Types::STRING, length: 255, nullable: false, enumType: VacancyGrabber::class, options: ['default' => 'dou'])]
    private VacancyGrabber $grabber = VacancyGrabber::Dou;

    #[ORM\OneToMany(targetEntity: Vacancy::class, mappedBy: 'source', cascade: ['persist'], orphanRemoval: false)]
    private Collection $vacancies;

    #[ORM\OneToMany(targetEntity: GrabLog::class, mappedBy: 'source', cascade: ['persist'], orphanRemoval: false)]
    private Collection $grabLogs;

    public function __construct()
    {
        $this->vacancies = new ArrayCollection();
        $this->grabLogs  = new ArrayCollection();
    }

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getName() ?? 'New Entity';

        return implode(' ', array_filter($labels));
    }

    public function getGrabber(): ?VacancyGrabber
    {
        return $this->grabber;
    }

    public function setGrabber(VacancyGrabber $grabber): static
    {
        $this->grabber = $grabber;

        return $this;
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getVacancies(): Collection
    {
        return $this->vacancies;
    }

    public function addVacancy(Vacancy $vacancy): static
    {
        if (!$this->vacancies->contains($vacancy)) {
            $this->vacancies->add($vacancy);
            $vacancy->setSource($this);
        }

        return $this;
    }

    public function removeVacancy(Vacancy $vacancy): static
    {
        if ($this->vacancies->removeElement($vacancy)) {
            // set the owning side to null (unless already changed)
            if ($vacancy->getSource() === $this) {
                $vacancy->setSource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GrabLog>
     */
    public function getGrabLogs(): Collection
    {
        return $this->grabLogs;
    }

    public function addGrabLog(GrabLog $grabLog): static
    {
        if (!$this->grabLogs->contains($grabLog)) {
            $this->grabLogs->add($grabLog);
            $grabLog->setSource($this);
        }

        return $this;
    }

    public function removeGrabLog(GrabLog $grabLog): static
    {
        if ($this->grabLogs->removeElement($grabLog)) {
            // set the owning side to null (unless already changed)
            if ($grabLog->getSource() === $this) {
                $grabLog->setSource(null);
            }
        }

        return $this;
    }
}
