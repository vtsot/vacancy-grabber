<?php

declare(strict_types=1);

namespace App\Entity;

use App\Contract\Dictionary\VacancyStatus;
use App\Entity\Traits\DescriptionEntityTrait;
use App\Entity\Traits\ExternalIdEntityTrait;
use App\Entity\Traits\SalaryEntityTrait;
use App\Entity\Traits\TitleEntityTrait;
use App\Entity\Traits\UrlEntityTrait;
use App\Entity\Traits\ViewedEntityTrait;
use App\Repository\VacancyRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function array_filter;
use function implode;

#[ORM\Table(name: 'vacancies')]
#[ORM\Index(name: 'fk_source_id', columns: ['source_id'])]
#[ORM\Index(name: 'fk_company_id', columns: ['company_id'])]
#[ORM\Index(name: 'idx_resume_sent_at', columns: ['resume_sent_at'])]
#[ORM\Index(name: 'idx_status', columns: ['status'])]
#[ORM\Entity(repositoryClass: VacancyRepository::class)]
class Vacancy extends AbstractEntity
{
    use TitleEntityTrait;
    use DescriptionEntityTrait;
    use ExternalIdEntityTrait;
    use UrlEntityTrait;
    use SalaryEntityTrait;
    use ViewedEntityTrait;

    #[ORM\ManyToOne(targetEntity: Source::class, inversedBy: 'vacancies')]
    #[ORM\JoinColumn(name: 'source_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Source $source = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'vacancies')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Company $company = null;

    #[ORM\Column(name: 'resume_sent_at', type: Types::DATETIME_IMMUTABLE, precision: 6, nullable: true)]
    private ?DateTimeImmutable $resumeSentAt;

    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'vacancy', cascade: ['persist'], orphanRemoval: false)]
    #[ORM\OrderBy(['notedAt' => 'ASC'])]
    private Collection $notes;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 255, nullable: false, enumType: VacancyStatus::class, options: ['default' => 'feedback'])]
    private VacancyStatus $status = VacancyStatus::Feedback;

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getTitle() ?? 'New Entity';

        return implode(' ', array_filter($labels));
    }

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getResumeSentAt(): ?DateTimeImmutable
    {
        return $this->resumeSentAt;
    }

    public function setResumeSentAt(?DateTimeImmutable $resumeSentAt): static
    {
        $this->resumeSentAt = $resumeSentAt;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

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
            $note->setVacancy($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getVacancy() === $this) {
                $note->setVacancy(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?VacancyStatus
    {
        return $this->status;
    }

    public function setStatus(VacancyStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
