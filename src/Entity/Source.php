<?php

declare(strict_types=1);

namespace App\Entity;

use App\Contract\Dictionary\VacancyGrabber;
use App\Entity\Traits\NameEntityTrait;
use App\Entity\Traits\UrlEntityTrait;
use App\Repository\SourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function array_filter;
use function implode;

#[ORM\Table(name: 'sources')]
#[ORM\Entity(repositoryClass: SourceRepository::class)]
class Source extends AbstractEntity
{
    use NameEntityTrait;
    use UrlEntityTrait;

    #[ORM\Column(name: 'grabber', type: Types::STRING, length: 255, nullable: false, enumType: VacancyGrabber::class, options: ['default' => 'dou'])]
    private VacancyGrabber $grabber = VacancyGrabber::Dou;

    #[ORM\OneToMany(targetEntity: Vacancy::class, mappedBy: 'source', cascade: ['persist'], orphanRemoval: true)]
    private Collection $vacancies;

    public function __construct()
    {
        $this->vacancies = new ArrayCollection();
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
}
