<?php

declare(strict_types=1);

namespace App\Domain\Source\Service;

use App\Domain\Note\Entity\Note;
use App\Domain\Note\Repository\NoteRepository;
use App\Domain\Shared\Exception\InvalidArgumentException;
use App\Domain\Source\Entity\Source;
use App\Domain\Source\Grabber\GrabbedVacancy;
use App\Domain\Vacancy\Entity\Vacancy;
use App\Domain\Vacancy\Repository\VacancyRepository;
use App\Domain\Vacancy\ValueObject\ExternalId;
use App\Domain\Vacancy\ValueObject\Salary;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

use function htmlspecialchars;
use function sprintf;

/**
 * Brings the vacancies of a source in line with the grabbed listings: when the title, description or salary of a
 * listing has changed, the stored vacancy takes the new text, gets a note with the old and new value of each changed field
 * and is marked unviewed so the owner sees it again.
 */
final readonly class VacancySynchronizer
{
    public function __construct(
        private VacancyRepository $vacancyRepository,
        private NoteRepository $noteRepository,
    ) {
    }

    /**
     * @param iterable<GrabbedVacancy> $grabbedVacancies
     * @param Uuid|null                $userId           sync only this owner's vacancies, null means every owner
     */
    public function sync(Source $source, iterable $grabbedVacancies, ?Uuid $userId = null): SyncResult
    {
        // the same listing may be imported by several owners
        /** @var array<string, list<Vacancy>> $vacanciesByExternalId */
        $vacanciesByExternalId = [];

        foreach ($this->vacancyRepository->findAllBySourceId($source->getId(), $userId) as $vacancy) {
            if ($externalId = $vacancy->getExternalId()) {
                $vacanciesByExternalId[$externalId->toString()][] = $vacancy;
            }
        }

        $vacanciesChecked = 0;
        $vacanciesUpdated = 0;

        foreach ($grabbedVacancies as $grabbed) {
            $externalId = ExternalId::fromNullable($grabbed->externalId);

            foreach ($externalId ? $vacanciesByExternalId[$externalId->toString()] ?? [] : [] as $vacancy) {
                $vacanciesChecked++;

                if ($notes = $this->applyChanges($vacancy, $grabbed)) {
                    $this->vacancyRepository->save($vacancy);

                    foreach ($notes as $note) {
                        $this->noteRepository->save($note);
                    }

                    $vacanciesUpdated++;
                }
            }
        }

        return new SyncResult($vacanciesChecked, $vacanciesUpdated);
    }

    /**
     * @return list<Note> a note with the old and new value of each changed field, empty when nothing changed
     */
    private function applyChanges(Vacancy $vacancy, GrabbedVacancy $grabbed): array
    {
        $notes = [];

        $salary = $this->grabbedSalary($grabbed);

        if ($salary && !($vacancy->getSalary() && $salary->equals($vacancy->getSalary()))) {
            $notes[] = $this->writeChangeNote($vacancy, 'salary', $vacancy->getSalary()?->toString(), $salary->toString());
            $vacancy->changeSalary($salary);
        }

        // an empty value means the grabber could not read it, not that the source cleared it
        if ($grabbed->title && $grabbed->title !== $vacancy->getTitle()) {
            $notes[] = $this->writeChangeNote($vacancy, 'title', $vacancy->getTitle(), $grabbed->title);
            $vacancy->rename($grabbed->title);
        }

        if ($grabbed->description && $grabbed->description !== $vacancy->getDescription()) {
            $notes[] = $this->writeChangeNote($vacancy, 'description', $vacancy->getDescription(), $grabbed->description);
            $vacancy->describe($grabbed->description);
        }

        if ($notes) {
            // writing a note marks the vacancy viewed, so this has to come after it
            $vacancy->markUnviewed();
        }

        return $notes;
    }

    private function grabbedSalary(GrabbedVacancy $grabbed): ?Salary
    {
        try {
            return Salary::fromNullable($grabbed->salary);
        } catch (InvalidArgumentException) {
            // an unreadable salary is skipped, as on import
            return null;
        }
    }

    /**
     * The note is shown as HTML, so the grabbed values are escaped and only the labels carry markup.
     */
    private function writeChangeNote(Vacancy $vacancy, string $field, ?string $oldValue, string $newValue): Note
    {
        return $vacancy->writeNote(
            author: $vacancy->getUser(),
            description: sprintf(
                "The vacancy was changed on the external source.\n<strong>Old %s:</strong> \n%s\n<strong>New %s:</strong> \n%s",
                $field,
                htmlspecialchars($oldValue ?? ''),
                $field,
                htmlspecialchars($newValue),
            ),
            notedAt: new DateTimeImmutable(),
        );
    }
}
