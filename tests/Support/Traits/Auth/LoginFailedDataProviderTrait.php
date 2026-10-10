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
            'data'     => ['username' => null, 'password' =>null],
            'expected' => [
                'code'       => HttpCode::BAD_REQUEST,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['username' => 'undefined', 'password' =>null],
            'expected' => [
                'code'       => HttpCode::BAD_REQUEST,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['username' => null, 'password' => 'undefined'],
            'expected' => [
                'code'       => HttpCode::BAD_REQUEST,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['username' => 'undefined', 'password' => 'undefined'],
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        return $data;
    }
}
