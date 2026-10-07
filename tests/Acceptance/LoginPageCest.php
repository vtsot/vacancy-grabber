<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;


use App\Tests\Support\AcceptanceTester;

class LoginPageCest
{
    public function loginFrom(AcceptanceTester $I): void
    {
        $I->amOnPage('/auth/login');
        $I->seeCurrentUrlEquals('/auth/login');
    }
}
