<?php

declare(strict_types=1);

namespace App\Tests\Suite\Api\Auth;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\ApiTester;
use App\Tests\Support\Traits\Auth\LoginFailedDataProviderTrait;
use App\Tests\Support\Traits\Auth\LoginSuccessDataProviderTrait;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

use function array_values;
use function Zenstruck\Foundry\faker;

class LoginAuthCest
{
    use LoginFailedDataProviderTrait;
    use LoginSuccessDataProviderTrait;

    #[DataProvider('loginFailedDataProvider')]
    public function loginFailed(ApiTester $I, Example $example): void
    {
        [$data, $expected]     = array_values((array) $example->getIterator());
        [$username, $password] = [$data['username'] ?? null, $data['password'] ?? null];

        $I->assertJsonRequest(
            url: '/api/auth/login',
            method: 'POST',
            expectedCode: $expected['code'] ?? HttpCode::OK,
            data: [
                'username' => $username,
                'password' => $password,
            ],
            jsonSchema: $expected['jsonSchema'] ?? null,
        );
    }

    #[DataProvider('frontLoginSuccessDataProvider')]
    public function loginSuccess(ApiTester $I, Example $example): void
    {
        [$roles, $expected] = array_values((array) $example->getIterator());

        $email = faker()->email();
        UserFactory::createUser(['roles' => $roles, 'email' => $email]);

        $I->assertJsonRequest(
            url: '/api/auth/login',
            method: 'POST',
            expectedCode: $expected['code'] ?? HttpCode::OK,
            data: [
                'username' => $email,
                'password' => 'password',
            ],
            jsonSchema: $expected['jsonSchema'] ?? null,
        );
    }
}
