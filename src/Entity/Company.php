<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\NameEntityTrait;
use App\Entity\Traits\UrlEntityTrait;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use function array_filter;
use function implode;

#[ORM\Table(name: 'companies')]
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company extends AbstractEntity
{
    use NameEntityTrait;
    use UrlEntityTrait;

    #[ORM\OneToMany(targetEntity: Vacancy::class, mappedBy: 'company', orphanRemoval: true)]
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
            $vacancy->setCompany($this);
        }

        return $this;
    }

    public function removeVacancy(Vacancy $vacancy): static
    {
        if ($this->vacancies->removeElement($vacancy)) {
            // set the owning side to null (unless already changed)
            if ($vacancy->getCompany() === $this) {
                $vacancy->setCompany(null);
            }
        }

        return $this;
    }
}
