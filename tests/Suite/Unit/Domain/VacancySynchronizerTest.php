<?php

declare(strict_types=1);

namespace App\Tests\Suite\Unit\Domain;

use App\Domain\Note\Entity\Note;
use App\Domain\Note\Repository\NoteRepository;
use App\Domain\Source\Entity\Source;
use App\Domain\Source\Grabber\GrabbedVacancy;
use App\Domain\Source\Service\VacancySynchronizer;
use App\Domain\User\Entity\User;
use App\Domain\Vacancy\Entity\Vacancy;
use App\Domain\Vacancy\Repository\VacancyRepository;
use App\Domain\Vacancy\ValueObject\ExternalId;
use App\Domain\Vacancy\ValueObject\Salary;
use Codeception\Test\Unit;
use ReflectionProperty;
use Symfony\Component\Uid\Uuid;

use function str_repeat;

final class VacancySynchronizerTest extends Unit
{
    /** @var list<Vacancy> */
    private array $storedVacancies = [];

    /** @var list<Vacancy> */
    private array $savedVacancies = [];

    /** @var list<Note> */
    private array $savedNotes = [];

    /** @var array{0: Uuid, 1: Uuid|null}|null */
    private ?array $requestedSourceAndUser = null;

    public function testUpdatesChangedTitleAndDescriptionAndMarksUnviewed(): void
    {
        $changed   = $this->stored('1', 'Old title', 'Old description');
        $unchanged = $this->stored('2', 'Same title', 'Same description');

        $result = $this->synchronizer()->sync($this->source(), [
            $this->grabbed('1', 'New title', 'New description'),
            $this->grabbed('2', 'Same title', 'Same description'),
            $this->grabbed('3', 'Not imported', 'Not imported'),
        ]);

        self::assertSame(2, $result->vacanciesChecked);
        self::assertSame(1, $result->vacanciesUpdated);
        self::assertSame([$changed], $this->savedVacancies);

        self::assertSame('New title', $changed->getTitle());
        self::assertSame('New description', $changed->getDescription());
        self::assertFalse($changed->isViewed());
        self::assertTrue($unchanged->isViewed());

        // one note per changed field, each with the old value
        self::assertCount(2, $this->savedNotes);
        self::assertSame(
            "The vacancy was changed on the external source.\n<strong>Old title:</strong> \nOld title\n<strong>New title:</strong> \nNew title",
            $this->savedNotes[0]->getDescription(),
        );
        self::assertSame(
            "The vacancy was changed on the external source.\n<strong>Old description:</strong> \nOld description\n<strong>New description:</strong> \nNew description",
            $this->savedNotes[1]->getDescription(),
        );
        self::assertNotNull($changed->getUser());

        foreach ($this->savedNotes as $note) {
            self::assertSame($changed, $note->getVacancy());
            self::assertSame($changed->getUser(), $note->getUser());
        }

        self::assertCount(2, $changed->getNotes());
        self::assertCount(0, $unchanged->getNotes());
    }

    public function testWritesOnlyTheNoteOfTheChangedField(): void
    {
        $vacancy = $this->stored('1', 'Title', 'Old description');

        $this->synchronizer()->sync($this->source(), [$this->grabbed('1', 'Title', 'New description')]);

        self::assertCount(1, $this->savedNotes);
        self::assertSame(
            "The vacancy was changed on the external source.\n<strong>Old description:</strong> \nOld description\n<strong>New description:</strong> \nNew description",
            $this->savedNotes[0]->getDescription(),
        );
        self::assertSame('Title', $vacancy->getTitle());
        self::assertFalse($vacancy->isViewed());
    }

    public function testUpdatesChangedSalaryWithItsOwnNote(): void
    {
        $vacancy = $this->stored('1', 'Title', 'Description', '$3000');

        $result = $this->synchronizer()->sync($this->source(), [$this->grabbed('1', 'Title', 'Description', '$4000')]);

        self::assertSame(1, $result->vacanciesUpdated);
        self::assertSame('$4000', $vacancy->getSalary()?->toString());
        self::assertFalse($vacancy->isViewed());
        self::assertCount(1, $this->savedNotes);
        self::assertSame(
            "The vacancy was changed on the external source.\n<strong>Old salary:</strong> \n$3000\n<strong>New salary:</strong> \n$4000",
            $this->savedNotes[0]->getDescription(),
        );
    }

    public function testNotesASalaryAddedOnTheSource(): void
    {
        $vacancy = $this->stored('1', 'Title', 'Description');

        $this->synchronizer()->sync($this->source(), [$this->grabbed('1', 'Title', 'Description', '$4000')]);

        self::assertSame('$4000', $vacancy->getSalary()?->toString());
        self::assertSame(
            "The vacancy was changed on the external source.\n<strong>Old salary:</strong> \n\n<strong>New salary:</strong> \n$4000",
            $this->savedNotes[0]->getDescription(),
        );
    }

    public function testKeepsSalaryWhenGrabbedSalaryIsMissingOrOnlyDiffersInSpacing(): void
    {
        $vacancy = $this->stored('1', 'Title', 'Description', '$3000 - $4000');

        $result = $this->synchronizer()->sync($this->source(), [
            $this->grabbed('1', 'Title', 'Description', null),
            $this->grabbed('1', 'Title', 'Description', '  $3000   -  $4000 '),
            $this->grabbed('1', 'Title', 'Description', str_repeat('9', 256)),
        ]);

        self::assertSame(0, $result->vacanciesUpdated);
        self::assertSame('$3000 - $4000', $vacancy->getSalary()?->toString());
        self::assertSame([], $this->savedNotes);
    }

    public function testEscapesGrabbedValuesInTheNote(): void
    {
        $this->stored('1', 'Title', '<p>Old</p>');

        $this->synchronizer()->sync($this->source(), [$this->grabbed('1', 'Title', '<script>alert(1)</script>')]);

        self::assertSame(
            "The vacancy was changed on the external source.\n<strong>Old description:</strong> \n&lt;p&gt;Old&lt;/p&gt;\n<strong>New description:</strong> \n&lt;script&gt;alert(1)&lt;/script&gt;",
            $this->savedNotes[0]->getDescription(),
        );
    }

    public function testKeepsStoredTextWhenGrabbedValueIsEmpty(): void
    {
        $vacancy = $this->stored('1', 'Title', 'Description');

        $result = $this->synchronizer()->sync($this->source(), [
            $this->grabbed('1', null, ''),
            $this->grabbed(null, 'Other', 'Other'),
        ]);

        self::assertSame(0, $result->vacanciesUpdated);
        self::assertSame('Title', $vacancy->getTitle());
        self::assertSame('Description', $vacancy->getDescription());
        self::assertTrue($vacancy->isViewed());
        self::assertSame([], $this->savedNotes);
    }

    public function testUpdatesEveryOwnerOfTheSameListing(): void
    {
        $first  = $this->stored('1', 'Old', 'Old');
        $second = $this->stored('1', 'Old', 'Old');

        $result = $this->synchronizer()->sync($this->source(), [$this->grabbed('1', 'New', 'Old')]);

        self::assertSame(2, $result->vacanciesUpdated);
        self::assertSame('New', $first->getTitle());
        self::assertSame('New', $second->getTitle());
        self::assertCount(2, $this->savedNotes);
        self::assertSame($first->getUser(), $this->savedNotes[0]->getUser());
        self::assertSame($second->getUser(), $this->savedNotes[1]->getUser());
    }

    public function testLoadsOnlyTheGivenOwnersVacancies(): void
    {
        $source = $this->source();
        $userId = Uuid::v7();

        $this->synchronizer()->sync($source, [], $userId);

        self::assertSame([$source->getId(), $userId], $this->requestedSourceAndUser);
    }

    private function synchronizer(): VacancySynchronizer
    {
        $vacancies = $this->createMock(VacancyRepository::class);
        $vacancies->method('findAllBySourceId')->willReturnCallback(function (Uuid $sourceId, ?Uuid $userId) {
            $this->requestedSourceAndUser = [$sourceId, $userId];

            return $this->storedVacancies;
        });
        $vacancies->method('save')->willReturnCallback(function (Vacancy $vacancy): void {
            $this->savedVacancies[] = $vacancy;
        });

        $notes = $this->createMock(NoteRepository::class);
        $notes->method('save')->willReturnCallback(function (Note $note): void {
            $this->savedNotes[] = $note;
        });

        return new VacancySynchronizer($vacancies, $notes);
    }

    private function stored(string $externalId, string $title, string $description, ?string $salary = null): Vacancy
    {
        $vacancy = Vacancy::create(User::register('owner@example.com'), $title);
        $vacancy->describe($description);
        $vacancy->changeSalary(Salary::fromNullable($salary));
        $vacancy->changeExternalId(ExternalId::fromString($externalId));
        $vacancy->markViewed();

        return $this->storedVacancies[] = $vacancy;
    }

    private function source(): Source
    {
        $source = new Source();
        new ReflectionProperty($source, 'id')->setValue($source, Uuid::v7());

        return $source;
    }

    private function grabbed(
        ?string $externalId,
        ?string $title,
        ?string $description,
        ?string $salary = null,
    ): GrabbedVacancy {
        return new GrabbedVacancy(
            externalId: $externalId,
            url: null,
            title: $title,
            description: $description,
            salary: $salary,
            company: null,
        );
    }
}
