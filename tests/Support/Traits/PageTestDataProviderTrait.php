<?php

declare(strict_types=1);

namespace App\Tests\Support\Traits;

use Codeception\Util\HttpCode;

trait PageTestDataProviderTrait
{
    private function publicPageDataProvider(): array
    {
        return [
            ['url' => '/admin/login', 'code' => HttpCode::OK],
            ['url' => '/auth/login', 'code' => HttpCode::OK],
            ['url' => '/auth/forgot-password', 'code' => HttpCode::OK],
        ];
    }

    private function adminPageDataProvider(): array
    {
        return [
            ['url' => '/admin', 'code' => HttpCode::FOUND],
            ['url' => '/admin/dashboard', 'code' => HttpCode::FOUND],
            ['url' => '/admin/search', 'code' => HttpCode::FOUND],
            ['url' => '/admin/core/get-autocomplete-items', 'code' => HttpCode::FOUND],
        ];
    }

    private function privatePageDataProvider(): array
    {
        return [
            ['url' => '/', 'code' => HttpCode::FOUND],
            ['url' => '/dashboard', 'code' => HttpCode::FOUND],
            ['url' => '/grab-logs', 'code' => HttpCode::FOUND],
            ['url' => '/notes', 'code' => HttpCode::FOUND],
            ['url' => '/sources', 'code' => HttpCode::FOUND],
            ['url' => '/vacancies', 'code' => HttpCode::FOUND],
        ];
    }

    private function pageTestDataProvider(): array
    {
        return [
            ...$this->publicPageDataProvider(),
            ...$this->adminPageDataProvider(),
            ...$this->privatePageDataProvider(),
        ];
    }

}
