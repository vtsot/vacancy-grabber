<?php

declare(strict_types=1);

namespace App\Tests\Suite\Acceptance;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\AcceptanceTester;
use App\Tests\Support\Traits\Auth\LoginFailedDataProviderTrait;
use App\Tests\Support\Traits\Auth\LoginSuccessDataProviderTrait;
use App\Tests\Support\Traits\PageTestDataProviderTrait;
use Codeception\Attribute\DataProvider;
use Codeception\Example;

use function is_array;
use function Zenstruck\Foundry\faker;

class SecurityPageCest
{
    use PageTestDataProviderTrait;
    use LoginFailedDataProviderTrait {
        loginFailedDataProvider as baseLoginFailedDataProvider;
    }
    use LoginSuccessDataProviderTrait;

    #[DataProvider('publicPageDataProvider')]
    public function publicPage(AcceptanceTester $I, Example $example): void
    {
        [$url] = array_values((array) $example->getIterator());

        $I->amOnPage($url);
        $I->seeCurrentUrlEquals($url);
    }

    #[DataProvider('privatePageDataProvider')]
    public function privatePage(AcceptanceTester $I, Example $example): void
    {
        [$url] = array_values((array) $example->getIterator());

        $I->amOnPage($url);
        $I->seeCurrentUrlEquals('/auth/login');
    }

    #[DataProvider('adminPageDataProvider')]
    public function adminPage(AcceptanceTester $I, Example $example): void
    {
        [$url] = array_values((array) $example->getIterator());

        $I->amOnPage($url);
        $I->seeCurrentUrlEquals('/admin/login');
    }

    private function loginFailedDataProvider(): array
    {
        $data = [];

        foreach ($this->privatePageDataProvider() as $privatePage) {
            foreach ($this->baseLoginFailedDataProvider() as $loginFailed) {
                $privatePage = [...$privatePage, ...$loginFailed];
                $data[]      = $privatePage;
            }
        }

        return $data;
    }

    private function loginSuccessDataProvider(): array
    {
        $data = [];

        foreach ($this->privatePageDataProvider() as $privatePage) {
            foreach ($this->frontLoginSuccessDataProvider() as $loginSuccess) {
                $privatePage = [...$privatePage, ...$loginSuccess];
                $data[]      = $privatePage;
            }
        }

        return $data;
    }

    #[DataProvider('privatePageDataProvider')]
    public function loginFailed(AcceptanceTester $I, Example $example): void
    {
        [$url, $expectedUrl, $title, $labels] = array_values((array) $example->getIterator());

        $I->amOnPage($url);
        $I->seeCurrentUrlEquals('/auth/login');

        $username = faker()->userName();
        $password = faker()->password();
        $I->tryLoginToFront($username, $password);
        $I->wait();

        match (true) {
            $username === null && $password === null,
            ($username === null && $password !== null) => $I->see('The key "username" must be a string'),
            ($username !== null && $password === null) => $I->see('The key "password" must be a string'),
            ($username !== null && $password !== null) => $I->see('Invalid credentials'),
        };
    }

    #[DataProvider('privatePageDataProvider')]
    public function loginSuccess(AcceptanceTester $I, Example $example): void
    {
        [$url, $expectedUrl, $title, $labels] = array_values((array) $example->getIterator());
        $I->amOnPage($url);
        $I->seeCurrentUrlEquals('/auth/login');

        $username = faker()->email();
        UserFactory::createUser(['email' => $username]);
        $I->loginToFrontAsUser($username);
        $I->wait();
        $I->seeInCurrentUrl($expectedUrl ?? $url);
        $I->see($title);

        $labels = !is_array($labels) ? (array) $labels : $labels;
        $labels = array_filter($labels);

        foreach ($labels as $label) {
            $I->see($label);
        }
    }
}
