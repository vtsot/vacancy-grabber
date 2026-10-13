<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\VacancyGrabber\Domain\User\Entity\User;
use App\VacancyGrabber\Domain\User\Repository\UserRepositoryInterface;
use App\VacancyGrabber\Domain\User\Entity\UserRole;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

use function array_merge;

/**
 * @extends PersistentObjectFactory<User>
 *
 * @method        User|Proxy     create(array|callable $attributes = [])
 * @method static User|Proxy     createOne(array $attributes = [])
 * @method static User[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy[] createSequence(iterable|callable $sequence)
 *
 * @method static User|Proxy     find(object|array|mixed $criteria)
 * @method static User|Proxy     findOrCreate(array $attributes)
 * @method static User[]|Proxy[] findBy(array $attributes)

 * @method static User|Proxy     first(string $sortedField = 'id')
 * @method static User|Proxy     last(string $sortedField = 'id')
 * @method static User[]|Proxy[] all()
 *
 * @method static User|Proxy     random(array $attributes = [])
 * @method static User|Proxy     randomOrCreate(array $attributes = [])
 * @method static User[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[] randomRangeOrCreate(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @method static UserRepository|ProxyRepositoryDecorator repository()
 */
final class UserFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'email'         => self::faker()->email(),
            'plainPassword' => 'password',
            'active'        => true,
            'firstName'     => self::faker()->firstName(),
            'lastName'      => self::faker()->lastName(),
            'roles'         => [UserRole::User],
        ];
    }

    public static function createSupervisor(array $attributes = []): User|Proxy
    {
        return self::createOne(
            array_merge(
                ['roles' => [UserRole::Supervisor->value]],
                $attributes
            )
        );
    }

    public static function createAdmin(array $attributes = []): User|Proxy
    {
        return self::createOne(
            array_merge(
                ['roles' => [UserRole::Admin->value]],
                $attributes
            )
        );
    }

    public static function createUser(array $attributes = []): User|Proxy
    {
        return self::createOne(
            array_merge(
                ['roles' => [UserRole::User->value]],
                $attributes
            )
        );
    }
}
