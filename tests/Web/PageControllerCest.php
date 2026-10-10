<?php

declare(strict_types=1);

namespace App\Tests\Web;

use App\Tests\Support\Traits\PageTestDataProviderTrait;
use App\Tests\Support\WebTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

class PageControllerCest
{
    use PageTestDataProviderTrait;

    #[DataProvider('pageTestDataProvider')]
    public function pageTest(WebTester $I, Example $example): void
    {
        [$url, $code] = array_values((array) $example->getIterator());

        $I->stopFollowingRedirects();
        $I->amOnPage($url);
        $I->seeResponseCodeIs($code);
        $I->startFollowingRedirects();
    }
}
