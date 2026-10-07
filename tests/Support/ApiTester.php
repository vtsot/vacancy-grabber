<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Codeception\Actor;
use Codeception\Util\HttpCode;
use Exception;
use Faker\Factory;
use Faker\Generator;

use function array_key_exists;
use function array_keys;
use function array_map;
use function array_values;
use function http_build_query;
use function str_replace;

class ApiTester extends Actor
{
    use _generated\ApiTesterActions;

    public function fake(): Generator
    {
        static $faker;

        if ($faker === null) {
            $faker = Factory::create();
        }

        return $faker;
    }

    public function haveApiHeaders(): void
    {
        $I = $this;
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->haveHttpHeader('Accept-Language', 'en_US');
    }

    private array $tokens = [];
    private array $refreshTokens = [];

    public function loginAsUser(string $username): void
    {
        if (array_key_exists($username, $this->tokens) === false) {
            $this->assertJsonRequest(
                url: '/api/auth/login',
                method: 'POST',
                expectedCode: HttpCode::OK,
                data: [
                         'username' => $username,
                         'password' => 'password',
                     ],
                jsonSchema: codecept_data_dir('schemas/auth/login.success.json'),
            );

            try {
                $token        = $this->grabDataFromResponseByJsonPath('$.token');
                $refreshToken = $this->grabDataFromResponseByJsonPath('$.refresh_token');

                $this->tokens[$username]        = $token[0];
                $this->refreshTokens[$username] = $refreshToken[0];
            } catch (Exception $e) {
                $this->fail($e->getMessage());
            }
        }

        $this->amBearerAuthenticated($this->tokens[$username]);
    }

    public function assertRequest(
        string $url,
        string $method,
        int $expectedCode,
        ?array $params = [],
        ?array $data = [],
        ?string $jsonSchema = null,
    ): void {
        if (!empty($params)) {
            $url = str_replace(
                    array_map(static fn(string $param): string => '{' . $param . '}', array_keys($params)),
                    array_values($params),
                    $url,
                ) . '?' . http_build_query($params);
        }

        match ($method) {
            'POST'   => $this->sendPost($url, $data),
            'PUT'    => $this->sendPut($url, $data),
            'PATCH'  => $this->sendPatch($url, $data),
            'GET'    => $this->sendGet($url, $data),
            'DELETE' => $this->sendDelete($url),
        };

        $this->seeResponseCodeIs($expectedCode);

        if ($jsonSchema !== null) {
            $this->seeResponseIsValidOnJsonSchema($jsonSchema);
        }
    }

    public function assertJsonRequest(
        string $url,
        string $method,
        int $expectedCode,
        ?array $params = [],
        ?array $data = [],
        ?string $jsonSchema = null,
    ): void {
        $this->haveApiHeaders();
        $this->assertRequest($url, $method, $expectedCode, $params, $data, $jsonSchema);
    }

//    public function assertCollectionMeta(
//        int $itemsCount,
//        int $totalItems,
//        int $currentPage,
//        int $itemsPerPage,
//    ): void {
//        $this->assertCount($itemsCount, $this->grabDataFromResponseByJsonPath('$.data.collection.items')[0]);
//        $this->assertEquals(
//            $totalItems,
//            $this->grabDataFromResponseByJsonPath('$.data.collection.meta.paginator.totalItems')[0],
//        );
//        $this->assertEquals(
//            $currentPage,
//            $this->grabDataFromResponseByJsonPath('$.data.collection.meta.paginator.itemsOffset')[0],
//        );
//        $this->assertEquals(
//            $itemsPerPage,
//            $this->grabDataFromResponseByJsonPath('$.data.collection.meta.paginator.itemsLimit')[0],
//        );
//    }

//    public function assertPropertiesAreNotNullable(mixed $object, ?array $exclude = null): void
//    {
//        if (is_object($object)) {
//            $reflection = new ReflectionClass($object);
//            $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
//
//            foreach ($properties as $property) {
//                $propertyValue = $property->getValue($object);
//
//                if (is_object($propertyValue) || is_array($propertyValue)) {
//                    $this->assertPropertiesAreNotNullable($propertyValue, $exclude);
//
//                    continue;
//                }
//
//                if ($exclude && in_array($property->name, $exclude, true)) {
//                    continue;
//                }
//
//                $this->assertNotNull(
//                    $propertyValue,
//                    sprintf('Class `%s` property `%s` is null', $property->class, $property->name),
//                );
//            }
//        } elseif (is_array($object)) {
//            foreach ($object as $value) {
//                if (is_object($value) || is_array($value)) {
//                    $this->assertPropertiesAreNotNullable($value, $exclude);
//
//                    continue;
//                }
//
//                $this->assertNotNull($value, 'Array value is null');
//            }
//        } else {
//            $this->fail('Unexpected object type');
//        }
//    }
//
//    public function assertNumberOfEmailsSent(int $expectedCount): void
//    {
//        /** @var MessageLoggerListener $logger */
//        $logger = $this->grabService('mailer.logger_message_listener');
//
//        $this->assertCount($expectedCount, $logger->getEvents()->getMessages());
//    }
//
//    public function assertEmailSubject(string $expected): void
//    {
//        /** @var MessageLoggerListener $logger */
//        $logger = $this->grabService('mailer.logger_message_listener');
//
//        $this->assertContains($expected, array_map(
//            static fn (Email $message): string => $message->getSubject(),
//            $logger->getEvents()->getMessages(),
//        ));
//    }
//
//    public function uploadFile(string $url, string $filePath): string
//    {
//        $fileName = basename($filePath);
//
//        $this->haveHttpHeader('Accept', 'application/json');
//        $this->haveHttpHeader('Content-Type', 'multipart/form-data; boundary=boundary');
//        $this->haveHttpHeader('Content-Disposition', sprintf('form-data; name="file"; filename="%s"', $fileName));
//
//        $this->sendPost($url, [], ['file' => new UploadedFile(path: $filePath, originalName: $fileName)]);
//
//        $this->seeResponseCodeIs(HttpCode::OK);
//        $this->seeResponseIsValidOnJsonSchema('tests/_schema/success/asset-uploaded.success.json');
//
//        return $this->grabDataFromResponseByJsonPath('$.data.name')[0];
//    }
}
