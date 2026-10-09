<?php

declare(strict_types=1);

namespace App\Tests\Api\Company;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\ApiTester;
use App\Tests\Support\Traits\Auth\LoginSuccessDataProviderTrait;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

use function array_values;
use function codecept_data_dir;
use function Zenstruck\Foundry\faker;

class CompanySummaryCest
{
    use LoginSuccessDataProviderTrait;

    public function collectionUnauthorizedFailed(ApiTester $I/* , Example $example */): void
    {
        $I->assertJsonRequest(
            url: '/api/companies/summary',
            method: 'GET',
            expectedCode: HttpCode::UNAUTHORIZED,
            jsonSchema: codecept_data_dir('schemas/error.json'),
        );
    }

    #[DataProvider('loginSuccessDataProvider')]
    public function collectionSuccess(ApiTester $I, Example $example): void
    {
        [
            $roles,
            $expected,
        ] = array_values((array) $example->getIterator());

        $email = faker()->email();
        UserFactory::createUser(['roles' => $roles, 'email' => $email]);

        $I->loginAsUser($email);
        $I->assertJsonCollection(
            url: '/api/companies/summary',
            method: 'GET',
            expectedCode: HttpCode::OK,
            jsonSchema: [
                codecept_data_dir('schemas/company/summary.collection.json'),
            ],
        );
    }
}
