<?php

declare(strict_types=1);

namespace App\Tests\Web\Auth;

use App\Tests\Support\WebTester;
use Codeception\Util\Locator;
use Facebook\WebDriver\WebDriverTargetLocator;

class AuthLoginControllerCest
{
    public function loginSuccess(WebTester $I): void
    {
        $I->amOnPage('/auth/login');
        //$I->

        //$form = Locator::contains(Locator::find('form', ['class' => 'col-md-12']), 'Slider');
        //$I->seeElement($sliderSection);
        $password = Locator::combine(
            "//form",
            "//label[contains(text(), 'password')]/following-sibling::input[@type='password']"
        );
    }
}
