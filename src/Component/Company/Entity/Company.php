<?php

declare(strict_types=1);

namespace App\Component\Company\Entity;

use App\Component\General\Shared\AbstractEntity;
use App\Component\General\Shared\EntityTrait\UrlEntityTrait;
use App\Component\Note\Entity\Note;
use App\Component\Vacancy\Entity\Vacancy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function array_filter;
use function implode;

#[ORM\Table(name: 'companies')]
#[ORM\UniqueConstraint(name: 'uniq_name', columns: ['name'])]
class Company extends AbstractEntity
{
    use UrlEntityTrait;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Vacancy::class, mappedBy: 'company', orphanRemoval: false)]
    private Collection $vacancies;

    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'company', cascade: ['persist'], orphanRemoval: false)]
    #[ORM\OrderBy(['notedAt' => 'ASC'])]
    private Collection $notes;

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getName() ?? 'New Entity';

        return implode(' ', array_filter($labels));
    }

    public function __construct()
    {
        $this->vacancies = new ArrayCollection();
        $this->notes     = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setCompany($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getCompany() === $this) {
                $note->setCompany(null);
            }
        }

        return $this;
    }
}
