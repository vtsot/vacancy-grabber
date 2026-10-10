<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\AcceptanceTester;
use App\Tests\Support\Traits\Auth\LoginSuccessDataProviderTrait;
use Codeception\Attribute\DataProvider;
use Codeception\Example;

use function array_values;
use function Zenstruck\Foundry\faker;

class AdminLoginSuccessCest
{
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
}
