<?php

declare(strict_types=1);

namespace App\Tests\Suite\Acceptance;

use App\Tests\Support\AcceptanceTester;
use App\Tests\Support\Traits\PageTestDataProviderTrait;
use Codeception\Attribute\DataProvider;
use Codeception\Example;

class PageControllerCest
{
    use PageTestDataProviderTrait;

    #[DataProvider('publicPageDataProvider')]
    public function publicPage(AcceptanceTester $I, Example $example): void
    {
        [$url] = array_values((array) $example->getIterator());

        $I->amOnPage($url);
        $I->seeCurrentUrlEquals($url);
    }

    #[DataProvider('privatePageDataProvider')]
    public function privatePage(AcceptanceTester $I, Example $example): void
    {
        [$url] = array_values((array) $example->getIterator());

        $I->amOnPage($url);
        $I->seeCurrentUrlEquals('/auth/login');
    }

    #[DataProvider('adminPageDataProvider')]
    public function adminPage(AcceptanceTester $I, Example $example): void
    {
        [$url] = array_values((array) $example->getIterator());

        $I->amOnPage($url);
        $I->seeCurrentUrlEquals('/admin/login');
    }
}
