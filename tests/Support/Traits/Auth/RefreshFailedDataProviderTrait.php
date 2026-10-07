<?php

declare(strict_types=1);

namespace App\Tests\Support\Traits\Auth;

use Codeception\Util\HttpCode;

trait RefreshFailedDataProviderTrait
{
    private function refreshFailedDataProvider(): array
    {
        // tested data
        $data = [];

        $data[] = [
            'data'     => [],
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['undefined-field'],
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['undefined-field' => 'undefined-value'],
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['refresh_token' => null],
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'data'     => ['refresh_token' => 'undefined-token'],
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        return $data;
    }
}
