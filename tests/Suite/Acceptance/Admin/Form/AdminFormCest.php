<?php

declare(strict_types=1);

namespace App\Tests\Suite\Acceptance\Admin\Form;

use App\Domain\Shared\ValueObject\Url;
use App\Domain\Source\Entity\Source;
use App\Domain\Source\Repository\SourceRepository;
use App\Domain\User\Entity\User;
use App\Domain\Vacancy\Entity\Vacancy;
use App\Domain\Vacancy\Repository\VacancyRepository;
use App\Tests\Factory\UserFactory;
use App\Tests\Support\AcceptanceTester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function password_verify;
use function sprintf;
use function uniqid;
use function Zenstruck\Foundry\faker;

/**
 * Sonata Admin edits setter-less entities through EntityFieldMap; these cover the write paths
 * (create/edit forms, value objects, domain errors as form errors, child admins, inline edits).
 */
class AdminFormCest
{
    public function _before(AcceptanceTester $I): void
    {
        $username = faker()->email();
        UserFactory::createAdmin(['email' => $username]);

        $I->tryLoginToAdmin($username, 'password');
        $I->wait();
        $I->seeCurrentUrlEquals('/admin/dashboard');
    }

    public function vacancyCreateAndEdit(AcceptanceTester $I): void
    {
        $title = 'Vacancy ' . uniqid();

        $I->amOnPage('/admin/vacancy/create');
        $I->fillField('input[id$="_title"]', $title);
        $I->fillField('input[id$="_externalId"]', ' ext-1 ');
        $I->fillField('input[id$="_url"]', 'https://example.com/jobs/1');
        $I->fillField('input[id$="_salary"]', '  $1000   -  2000 ');
        $I->click('button[name="btn_create_and_edit"]');
        $I->wait();
        $I->see('successfully created');

        $vacancy = $this->findVacancy($I, $title);
        $I->assertSame('ext-1', $vacancy->getExternalId()?->toString());
        $I->assertSame('https://example.com/jobs/1', $vacancy->getUrl()?->toString());
        $I->assertSame('$1000 - 2000', $vacancy->getSalary()?->toString());

        // the edit form shows value objects as plain text
        $I->seeInField('input[id$="_url"]', 'https://example.com/jobs/1');

        // a value the domain rejects becomes a field error, nothing is saved
        $I->fillField('input[id$="_title"]', $title . ' changed');
        $I->fillField('input[id$="_url"]', 'not a url');
        $I->click('button[name="btn_update_and_edit"]');
        $I->wait();
        $I->see('is not a valid URL');
        $I->assertSame($title, $this->findVacancy($I, $title)->getTitle());

        // a valid edit is saved
        $I->fillField('input[id$="_title"]', $title . ' changed');
        $I->fillField('input[id$="_url"]', 'https://example.com/jobs/2');
        $I->click('button[name="btn_update_and_edit"]');
        $I->wait();
        $I->see('successfully updated');

        $vacancy = $this->findVacancy($I, $title . ' changed');
        $I->assertSame('https://example.com/jobs/2', $vacancy->getUrl()?->toString());
    }

    public function vacancyNoteIsAttachedToParent(AcceptanceTester $I): void
    {
        $vacancy = Vacancy::create(owner: null, title: 'Vacancy ' . uniqid());
        $I->grabService(VacancyRepository::class)->save($vacancy);

        $I->amOnPage(sprintf('/admin/vacancy/%s/vacancy-note/create', $vacancy->getId()));
        $I->executeJS('document.querySelector(\'input[id$="_notedAt"]\').value = "2026-10-01"');
        $I->fillField('textarea[id$="_description"]', 'Called the recruiter');
        $I->click('button[name="btn_create_and_edit"]');
        $I->wait();
        $I->see('successfully created');

        $em = $I->grabService(EntityManagerInterface::class);
        $em->clear();
        $notes = $em->find(Vacancy::class, $vacancy->getId())->getNotes();

        $I->assertCount(1, $notes);
        $I->assertSame('Called the recruiter', $notes->first()->getDescription());
    }

    public function userCreateHashesPassword(AcceptanceTester $I): void
    {
        $email = faker()->email();

        $I->amOnPage('/admin/user-regular/create');
        $I->fillField('input[id$="_email"]', $email);
        $I->fillField('input[id$="_firstName"]', 'Jane');
        $I->fillField('input[id$="_plainPassword_first"]', 'secret-123');
        $I->fillField('input[id$="_plainPassword_second"]', 'secret-123');
        $I->click('button[name="btn_create_and_edit"]');
        $I->wait();
        $I->see('successfully created');

        $em = $I->grabService(EntityManagerInterface::class);
        $em->clear();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        $I->assertSame('Jane', $user->getFirstName());
        $I->assertTrue(password_verify('secret-123', (string) $user->getPassword()));
    }

    public function inlineEditUsesIntentMethods(AcceptanceTester $I): void
    {
        $source = Source::create(owner: null, name: 'Source ' . uniqid(), url: Url::fromString('https://example.com'));

        /** @var PropertyAccessorInterface $accessor */
        $accessor = $I->grabService('property_accessor');

        $I->assertTrue($accessor->isWritable($source, 'active'));
        $accessor->setValue($source, 'active', false);
        $I->assertFalse($source->isActive());

        $I->assertSame('https://example.com', $accessor->getValue($source, 'url'));
        $I->grabService(SourceRepository::class)->save($source);
    }

    private function findVacancy(AcceptanceTester $I, string $title): Vacancy
    {
        $em = $I->grabService(EntityManagerInterface::class);
        $em->clear();

        return $em->getRepository(Vacancy::class)->findOneBy(['title' => $title]);
    }
}
