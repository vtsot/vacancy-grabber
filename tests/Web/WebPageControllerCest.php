<?php

declare(strict_types=1);

namespace App\Tests\Web;

use App\Tests\Support\WebTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

class WebPageControllerCest
{
    private function pageTestDataProvider(): array
    {
        return [
            ['url' => '/admin/login', 'code' => HttpCode::OK],
            ['url' => '/auth/login', 'code' => HttpCode::OK],
            ['url' => '/auth/forgot-password', 'code' => HttpCode::OK],
            ['url' => '/', 'code' => HttpCode::FOUND],
            ['url' => '/admin', 'code' => HttpCode::FOUND],
            ['url' => '/admin/dashboard', 'code' => HttpCode::FOUND],
            ['url' => '/companies', 'code' => HttpCode::FOUND],
            ['url' => '/dashboard', 'code' => HttpCode::FOUND],
            ['url' => '/grab-logs', 'code' => HttpCode::FOUND],
            ['url' => '/notes', 'code' => HttpCode::FOUND],
            ['url' => '/sources', 'code' => HttpCode::FOUND],
            ['url' => '/vacancies', 'code' => HttpCode::FOUND],
        ];
    }

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
