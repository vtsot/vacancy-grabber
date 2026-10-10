<?php

declare(strict_types=1);

namespace App\Tests\Suite\Api\Auth;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\ApiTester;
use App\Tests\Support\Traits\Auth\LoginSuccessDataProviderTrait;
use App\Tests\Support\Traits\Auth\RefreshFailedDataProviderTrait;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;
use JsonException;

use function json_decode;
use function Zenstruck\Foundry\faker;

use const JSON_THROW_ON_ERROR;

class RefreshTokenAuthCest
{
    use LoginSuccessDataProviderTrait;
    use RefreshFailedDataProviderTrait;

    #[DataProvider('refreshFailedDataProvider')]
    public function refreshFailed(ApiTester $I, Example $example): void
    {
        [$data, $expected] = array_values((array) $example->getIterator());

        $I->assertJsonRequest(
            url: '/api/auth/refresh-token',
            method: 'POST',
            expectedCode: $expected['code'] ?? HttpCode::OK,
            data: $data,
            jsonSchema: $expected['jsonSchema'] ?? null,
        );
    }

    /**
     * @throws JsonException
     */
    #[DataProvider('frontLoginSuccessDataProvider')]
    public function refreshSuccess(ApiTester $I, Example $example): void
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

        // grab refresh token
        $response     = json_decode(json: $I->grabResponse(), associative: true, flags: JSON_THROW_ON_ERROR);
        $refreshToken = $response['refresh_token'];

        $I->assertJsonRequest(
            url: '/api/auth/refresh-token',
            method: 'POST',
            expectedCode: HttpCode::OK,
            data: [
                'refresh_token' => $refreshToken,
            ],
            jsonSchema: codecept_data_dir('schemas/auth/login.success.json'),
        );
    }
}
