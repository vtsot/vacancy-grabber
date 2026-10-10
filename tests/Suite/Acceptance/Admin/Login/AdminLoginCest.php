<?php

declare(strict_types=1);

namespace App\Tests\Suite\Acceptance\Admin\Login;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\AcceptanceTester;

use function Zenstruck\Foundry\faker;

class AdminLoginCest
{
    public function userLoginFailed(AcceptanceTester $I): void
    {
        $username = faker()->email();
        UserFactory::createUser(['email' => $username]);

        $I->tryLoginToAdmin($username, 'password');
        $I->see('403 Forbidden');
    }

    public function adminLoginSuccess(AcceptanceTester $I): void
    {
        $username = faker()->email();
        UserFactory::createAdmin(['email' => $username]);

        $I->tryLoginToAdmin($username, 'password');
        $I->wait();

        $I->seeCurrentUrlEquals('/admin/dashboard');
        $I->see('Logout');
        $I->seeLink('Logout');
    }

    public function supervisorLoginSuccess(AcceptanceTester $I): void
    {
        $username = faker()->email();
        UserFactory::createSupervisor(['email' => $username]);

        $I->tryLoginToAdmin($username, 'password');
        $I->wait();

        $I->seeCurrentUrlEquals('/admin/dashboard');
        $I->see('Logout');
        $I->seeLink('Logout');
    }
}
