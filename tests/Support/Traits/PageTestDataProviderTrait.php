<?php

declare(strict_types=1);

namespace App\Tests\Support\Traits;

use Codeception\Util\HttpCode;

trait PageTestDataProviderTrait
{
    private function publicPageDataProvider(): array
    {
        return [
            ['url' => '/admin/login'],
            ['url' => '/auth/login'],
            ['url' => '/auth/forgot-password'],
        ];
    }

    private function adminPageDataProvider(): array
    {
        return [
            ['url' => '/admin'],
            ['url' => '/admin/dashboard'],
            ['url' => '/admin/search'],
            ['url' => '/admin/core/get-autocomplete-items'],
        ];
    }

    private function privatePageDataProvider(): array
    {
        return [
            ['url' => '/', 'expectedUrl' => '/dashboard',   'title' => 'Dashboard',  'labels' => ['Vacancy Status', 'Resume Sent']],
            ['url' => '/dashboard',  'expectedUrl' => null, 'title' => 'Dashboard',  'labels' => ['Vacancy Status', 'Resume Sent']],
            ['url' => '/sources',    'expectedUrl' => null, 'title' => 'Sources',    'labels' => 'Filters'],
            ['url' => '/grab-logs',  'expectedUrl' => null, 'title' => 'Grab logs',  'labels' => 'Filters'],
            ['url' => '/vacancies',  'expectedUrl' => null, 'title' => 'Vacancies',  'labels' => 'Filters'],
            ['url' => '/companies',  'expectedUrl' => null, 'title' => 'Companies',  'labels' => 'Filters'],
            ['url' => '/notes',      'expectedUrl' => null, 'title' => 'Notes',      'labels' => 'Filters'],
        ];
    }

    private function pageTestDataProvider(): array
    {
        $publicPageDataProviderCodes  = ['code' => HttpCode::OK];
        $adminPageDataProviderCodes   = ['code' => HttpCode::FOUND];
        $privatePageDataProviderCodes = ['code' => HttpCode::FOUND,];

        return [
            ... array_map(
                static fn(array $item): array => [... $item, ... $publicPageDataProviderCodes],
                $this->publicPageDataProvider(),
            ),
            ... array_map(
                static fn(array $item): array => [... $item, ... $adminPageDataProviderCodes],
                $this->adminPageDataProvider(),
            ),
            ... array_map(
                static fn(array $item): array => [... $item, ... $privatePageDataProviderCodes],
                $this->privatePageDataProvider(),
            ),
        ];
    }
}
