<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

//use App\Tests\AcceptanceTester;

use App\Tests\Support\AcceptanceTester;

class HomePageCest
{
    public function homepageSuccess(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->seeCurrentUrlEquals('/auth/login');
    }
}
