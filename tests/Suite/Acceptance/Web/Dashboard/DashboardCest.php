<?php

declare(strict_types=1);

namespace App\Tests\Suite\Acceptance\Web\Dashboard;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\AcceptanceTester;
use App\Tests\Support\Traits\Auth\LoginFailedDataProviderTrait;
use App\Tests\Support\Traits\Auth\LoginSuccessDataProviderTrait;
use Codeception\Attribute\DataProvider;
use Codeception\Example;

use function Zenstruck\Foundry\faker;

class DashboardCest
{
    use LoginFailedDataProviderTrait;
    use LoginSuccessDataProviderTrait;

    public function unauthorizedUserFailed(AcceptanceTester $I): void
    {
        $I->amOnPage('/dashboard');
        $I->seeCurrentUrlEquals('/auth/login');
    }

    #[DataProvider('loginFailedDataProvider')]
    public function loginFailed(AcceptanceTester $I, Example $example): void
    {
        $I->amOnPage('/dashboard');
        $I->seeCurrentUrlEquals('/auth/login');

        [$data] = array_values((array)$example->getIterator());
        [$username, $password] = [$data['username'] ?? null, $data['password'] ?? null];
        $I->tryLoginToFront($username, $password);
        $I->wait();

        match (true) {
            ($username === null && $password === null),
            ($username === null && $password !== null) => $I->see('The key "username" must be a string'),
            ($username !== null && $password === null) => $I->see('The key "password" must be a string'),
            ($username !== null && $password !== null) => $I->see('Invalid credentials'),
        };

        $I->makeScreenshot('loginFailed-' . $username . $password);
    }

    #[DataProvider('frontLoginSuccessDataProvider')]
    public function loginSuccess(AcceptanceTester $I, Example $example): void
    {
        $I->amOnPage('/dashboard');
        $I->seeCurrentUrlEquals('/auth/login');

        [$roles]  = array_values((array)$example->getIterator());
        $username = faker()->email();
        UserFactory::createUser(['roles' => $roles, 'email' => $username]);

        $I->loginToFrontAsUser($username);
        $I->wait();

        $I->see('Dashboard');
        $I->see('Vacancy Status');
        $I->see('Resume Sent');
    }
}
