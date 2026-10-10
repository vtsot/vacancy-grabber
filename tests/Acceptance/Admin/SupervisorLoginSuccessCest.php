<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\AcceptanceTester;

use function Zenstruck\Foundry\faker;

class SupervisorLoginSuccessCest
{
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
