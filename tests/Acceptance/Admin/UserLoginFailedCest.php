<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\AcceptanceTester;

use function Zenstruck\Foundry\faker;

class UserLoginFailedCest
{
    public function userLoginFailed(AcceptanceTester $I): void
    {
        $username = faker()->email();
        UserFactory::createUser(['email' => $username]);

        $I->tryLoginToAdmin($username, 'password');
        $I->see('Bad credentials');
    }
}
