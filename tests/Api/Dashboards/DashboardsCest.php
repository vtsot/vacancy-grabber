<?php

declare(strict_types=1);

namespace App\Tests\Api\Dashboards;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\ApiTester;
use App\Tests\Support\Traits\Auth\LoginFailedDataProviderTrait;
use App\Tests\Support\Traits\Auth\LoginSuccessDataProviderTrait;
use App\Tests\Support\Traits\Dashboards\DashboardUnauthorizedFailedDataProviderTrait;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

use function array_values;
use function codecept_data_dir;
use function Zenstruck\Foundry\faker;

class DashboardsCest
{
    use LoginFailedDataProviderTrait;
    use LoginSuccessDataProviderTrait;
    use DashboardUnauthorizedFailedDataProviderTrait;

    #[DataProvider('dashboardUnauthorizedFailedDataProvider')]
    public function dashboardUnauthorizedFailed(ApiTester $I, Example $example): void
    {
        [$url, $expected] = array_values((array) $example->getIterator());

        $I->assertJsonRequest(
            url: $url,
            method: 'GET',
            expectedCode: $expected['code'] ?? HttpCode::OK,
            jsonSchema: $expected['jsonSchema'] ?? null,
        );
    }

    #[DataProvider('loginSuccessDataProvider')]
    public function dashboardSuccess(ApiTester $I, Example $example): void
    {
        [$roles, $expected] = array_values((array) $example->getIterator());

        $email = faker()->email();
        UserFactory::createUser(['roles' => $roles, 'email' => $email]);

        $I->loginAsUser($email);
        $I->assertJsonRequest(
            url: '/api/dashboards',
            method: 'GET',
            expectedCode: $expected['code'] ?? HttpCode::OK,
            jsonSchema: codecept_data_dir('schemas/dashboards/dashboards.success.json'),
        );
    }

    #[DataProvider('loginSuccessDataProvider')]
    public function sentResumeToAllResumeZoomableSuccess(ApiTester $I, Example $example): void
    {
        [$roles, $expected] = array_values((array) $example->getIterator());

        $email = faker()->email();
        UserFactory::createUser(['roles' => $roles, 'email' => $email]);

        $I->loginAsUser($email);
        $I->assertJsonRequest(
            url: '/api/dashboards/sent-resume-to-all-resume-zoomable',
            method: 'GET',
            expectedCode: $expected['code'] ?? HttpCode::OK,
            jsonSchema: codecept_data_dir('schemas/dashboards/dashboards.success.sent-resume-to-all-resume-zoomable.json'),
        );
    }

    #[DataProvider('loginSuccessDataProvider')]
    public function vacancyStatusSuccess(ApiTester $I, Example $example): void
    {
        [$roles, $expected] = array_values((array) $example->getIterator());

        $email = faker()->email();
        UserFactory::createUser(['roles' => $roles, 'email' => $email]);

        $I->loginAsUser($email);
        $I->assertJsonRequest(
            url: '/api/dashboards/vacancy-status',
            method: 'GET',
            expectedCode: $expected['code'] ?? HttpCode::OK,
            jsonSchema: codecept_data_dir('schemas/dashboards/dashboards.success.vacancy-status.json'),
        );
    }
}
