<?php

declare(strict_types=1);

namespace App\Tests\Support\Traits\Auth;

use Codeception\Util\HttpCode;

trait LoginFailedDataProviderTrait
{
    private function loginFailedDataProvider(): array
    {
        // tested data
        $data = [];

        $data[] = [
            'data'     => [],
            'expected' => [
                'code'       => HttpCode::BAD_REQUEST,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['undefined-field'],
            'expected' => [
                'code'       => HttpCode::BAD_REQUEST,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['undefined-field' => 'undefined-value'],
            'expected' => [
                'code'       => HttpCode::BAD_REQUEST,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['password'],
            'expected' => [
                'code'       => HttpCode::BAD_REQUEST,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['username' => null, 'password' => null],
            'expected' => [
                'code'       => HttpCode::BAD_REQUEST,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['username' => 'undefined-username', 'password' => 'undefined-password'],
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['username' => 'undefined-username', 'password' => 'undefined-password'],
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/auth/login.error.json'),
            ],
        ];

        return $data;
    }

}
