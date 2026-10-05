<?php

declare(strict_types=1);

namespace App\Component\GrabLog\Security\Voter;

use App\Component\GrabLog\Entity\GrabLog;
use App\Component\GrabLog\Repository\GrabLogRepository;
use App\Component\User\Contract\Dictionary\UserRole;
use App\Component\User\Contract\Entity\UserInterface;
use App\Component\User\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Uid\Uuid;

use function array_key_exists;
use function in_array;
use function is_array;

class GrabLogVoter extends Voter
{
    public const string LIST   = 'GRAB_LOG_LIST';
    public const string CREATE = 'GRAB_LOG_CREATE';
    public const string VIEW   = 'GRAB_LOG_VIEW';
    public const string UPDATE = 'GRAB_LOG_UPDATE';
    public const string DELETE = 'GRAB_LOG_DELETE';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly GrabLogRepository $grabLogRepository,
    ) {
    }

    /**
     * @param array{
     *     0: Uuid|null,        # userId
     *     1: string|GrabLog    # subject
     * } $subject
     */
    protected function supports($attribute, $subject): bool
    {
        return is_array($subject)
            && array_key_exists(0, $subject)
            && array_key_exists(1, $subject)
            && ($subject[0] instanceof Uuid || $subject[0] === null)
            && ($subject[1] === GrabLog::class || $subject[1] instanceof GrabLog)
            && (
                (in_array($attribute, [self::LIST, self::CREATE], true) && $subject[1] === GrabLog::class)
                || (in_array(
                    $attribute,
                    [self::VIEW, self::UPDATE, self::DELETE],
                    true
                ) && $subject[1] instanceof GrabLog)
            );
    }

    /**
     * @param array{
     *     0: Uuid|null,        # userId
     *     1: string|GrabLog    # subject
     * } $subject
     */
    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        [$userId, $subject] = $subject;

        if (($user = $userId ? $this->userRepository->findOneById($userId) : null)
            && !$user instanceof UserInterface
        ) {
            return false;
        }

        return match ($attribute) {
            self::LIST,
            self::CREATE => true,

            self::VIEW,
            self::UPDATE,
            self::DELETE => ($user->hasRole(UserRole::Supervisor->value) || $user->hasRole(UserRole::Admin->value))
                || ($subject instanceof GrabLog
                && $this->grabLogRepository->findOneByIdAndUserId($subject->getId(), $user->getId()) !== null
                && $user->getId() && $user->getId()->equals($subject->getUser()->getId())
                ),

            default => false,
        };
    }
}
