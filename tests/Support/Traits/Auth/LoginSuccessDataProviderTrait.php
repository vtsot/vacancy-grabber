<?php

declare(strict_types=1);

namespace App\Tests\Support\Traits\Auth;

use App\Domain\User\Entity\UserRole;
use Codeception\Util\HttpCode;

use function array_map;

trait LoginSuccessDataProviderTrait
{
    private function adminLoginSuccessDataProvider(): array
    {
        // tested data
        $data   = [];
        $data[] = ['roles' => [UserRole::Supervisor->value]];
        $data[] = ['roles' => [UserRole::Admin->value]];

        return $data;
    }

    private function adminLoginFailedDataProvider(): array
    {
        // tested data
        $data   = [];
        $data[] = ['roles' => [UserRole::User->value]];

        return $data;
    }

    private function frontLoginSuccessDataProvider(): array
    {
        $expected = [
            'expected' => [
                'code'       => HttpCode::OK,
                'jsonSchema' => codecept_data_dir('schemas/auth/login.success.json'),
            ],
        ];

        return [
            ... array_map(
                static fn(array $item): array => [... $item, ... $expected],
                $this->adminLoginSuccessDataProvider(),
            ),
            ... array_map(
                static fn(array $item): array => [... $item, ... $expected],
                $this->adminLoginFailedDataProvider(),
            ),
        ];
    }
}
