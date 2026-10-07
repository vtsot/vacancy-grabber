<?php

declare(strict_types=1);

namespace App\Tests\Support\Traits\Dashboards;

use Codeception\Util\HttpCode;

trait DashboardUnauthorizedFailedDataProviderTrait
{
    private function dashboardUnauthorizedFailedDataProvider(): array
    {
        // tested data
        $data = [];

        $data[] = [
            'url'      => '/api/dashboards',
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'url'      => '/api/dashboards/sent-resume-to-all-resume-zoomable',
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        $data[] = [
            'url'      => '/api/dashboards/vacancy-status',
            'expected' => [
                'code'       => HttpCode::UNAUTHORIZED,
                'jsonSchema' => codecept_data_dir('schemas/error.json'),
            ],
        ];

        return $data;
    }
}
