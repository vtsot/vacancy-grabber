<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\AcceptanceTester;
use App\Tests\Support\Traits\Auth\LoginFailedDataProviderTrait;
use App\Tests\Support\Traits\Auth\LoginSuccessDataProviderTrait;
use Codeception\Attribute\DataProvider;
use Codeception\Example;

use function array_values;
use function Zenstruck\Foundry\faker;

class AdminLoginCest
{
    use LoginFailedDataProviderTrait;
    use LoginSuccessDataProviderTrait;

    #[DataProvider('loginFailedDataProvider')]
    public function loginFailed(AcceptanceTester $I, Example $example): void
    {
        [$data] = array_values((array)$example->getIterator());
        [$username, $password] = [$data[0] ?? null, $data[1] ?? null];

        $I->tryLoginToAdmin($username, $password);
        match (true) {
            $username === null && $password === null => $I->see('The key "_username" must be a non-empty string'),
            $username !== null && $password === null => $I->see('The key "_password" must be a non-empty string'),
            $username !== null && $password !== null => $I->see('Bad credentials'),
            default                                  => static fn() => null,
        };
    }

    #[DataProvider('adminLoginFailedDataProvider')]
    public function adminLoginFailed(AcceptanceTester $I, Example $example): void
    {
        [$roles]  = array_values((array)$example->getIterator());
        $username = faker()->email();
        UserFactory::createUser(['roles' => $roles, 'email' => $username]);

        $I->tryLoginToAdmin($username, 'password');
        $I->see('Bad credentials');
    }

    #[DataProvider('adminLoginSuccessDataProvider')]
    public function loginSuccess(AcceptanceTester $I, Example $example): void
    {
        [$roles]  = array_values((array)$example->getIterator());
        $username = faker()->email();
        UserFactory::createUser(['roles' => $roles, 'email' => $username]);

        $I->loginToAdminAsAdmin($username);
        $I->seeLink('Logout');
        $I->click('Logout');
        $I->wait();

        $I->seeCurrentUrlEquals('/admin/login');
        $I->dontSee('Logout');
        $I->see('Login');
    }
}
