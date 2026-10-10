<?php

declare(strict_types=1);

namespace App\Tests\Suite\Acceptance;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\AcceptanceTester;
use App\Tests\Support\Traits\PageTestDataProviderTrait;
use Codeception\Attribute\DataProvider;
use Codeception\Example;

use function array_filter;
use function is_array;
use function Zenstruck\Foundry\faker;

class PageCest
{
    use PageTestDataProviderTrait;

    #[DataProvider('privatePageDataProvider')]
    public function privatePageFailed(AcceptanceTester $I, Example $example): void
    {
        [$url] = array_values((array)$example->getIterator());
        $I->amOnPage($url);
        $I->seeCurrentUrlEquals('/auth/login');

        $username = faker()->email();
        $password = faker()->password();
        $I->tryLoginToFront($username, $password);
        $I->wait();
        $I->see('Invalid credentials');
    }

    #[DataProvider('privatePageDataProvider')]
    public function privatePageSuccess(AcceptanceTester $I, Example $example): void
    {
        [$url, $expectedUrl, $title, $labels] = array_values((array)$example->getIterator());
        $I->amOnPage($url);
        $I->seeCurrentUrlEquals('/auth/login');

        $username = faker()->email();
        UserFactory::createUser(['email' => $username]);

        $I->loginToFrontAsUser($username);
        $I->wait();
        $I->see($title);

        $labels = !is_array($labels) ? (array)$labels : $labels;
        $labels = array_filter($labels);
        foreach ($labels as $label) {
            $I->see($label);
        }
    }
}
