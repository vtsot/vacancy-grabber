<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Security\UserProvider;

use App\VacancyGrabber\Domain\User\Repository\UserRepository;
use App\VacancyGrabber\Infrastructure\Security\SecurityUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use function get_class;
use function sprintf;

/**
 * @implements UserProviderInterface<SecurityUser>
 */
abstract class AbstractUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): SecurityUser
    {
        $user = $this->userRepository->findOneByIdentifier($identifier);

        if ($user === null) {
            $e = new UserNotFoundException(sprintf('User with id `%s` not found.', $identifier));
            $e->setUserIdentifier($identifier);

            throw $e;
        }

        return new SecurityUser($user);
    }

    /**
     * @throws UserNotFoundException    if the user is not found
     * @throws UnsupportedUserException if the user is not supported
     */
    public function refreshUser(UserInterface $user): SecurityUser
    {
        if (!$user instanceof SecurityUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $refreshedUser = $user->getId() ? $this->userRepository->findOneById($user->getId()) : null;

        if ($refreshedUser === null) {
            $e = new UserNotFoundException(sprintf('User with id `%s` not found.', $user->getUserIdentifier()));
            $e->setUserIdentifier($user->getUserIdentifier());

            throw $e;
        }

        return new SecurityUser($refreshedUser);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof SecurityUser) {
            return;
        }

        $user->getUser()->rehashPassword($newHashedPassword);
        $this->userRepository->save($user->getUser());
    }

    public function supportsClass(string $class): bool
    {
        return $class === SecurityUser::class;
    }
}
