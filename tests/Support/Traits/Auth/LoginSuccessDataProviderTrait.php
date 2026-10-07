<?php

declare(strict_types=1);

namespace App\Tests\Support\Traits\Auth;

use App\Component\User\Contract\Dictionary\UserRole;
use Codeception\Util\HttpCode;

trait LoginSuccessDataProviderTrait
{
    private function loginSuccessDataProvider(): array
    {
        // tested data
        $data = [];

        $data[] = [
            'roles'    => [UserRole::Supervisor],
            'expected' => [
                'code'       => HttpCode::OK,
                'jsonSchema' => codecept_data_dir('schemas/auth/login.success.json'),
            ],
        ];

        $data[] = [
            'roles'    => [UserRole::Admin],
            'expected' => [
                'code'       => HttpCode::OK,
                'jsonSchema' => codecept_data_dir('schemas/auth/login.success.json'),
            ],
        ];

        $data[] = [
            'roles'    => [UserRole::User],
            'expected' => [
                'code'       => HttpCode::OK,
                'jsonSchema' => codecept_data_dir('schemas/auth/login.success.json'),
            ],
        ];

        return $data;
    }
}
