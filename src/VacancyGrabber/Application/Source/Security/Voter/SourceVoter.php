<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\Security\Voter;

use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepositoryInterface;
use App\VacancyGrabber\Domain\User\Entity\UserRole;
use App\VacancyGrabber\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Uid\Uuid;

use function array_key_exists;
use function in_array;
use function is_array;

class SourceVoter extends Voter
{
    public const string LIST   = 'SOURCE_LIST';
    public const string CREATE = 'SOURCE_CREATE';
    public const string GRAB   = 'SOURCE_GRAB';
    public const string VIEW   = 'SOURCE_VIEW';
    public const string UPDATE = 'SOURCE_UPDATE';
    public const string DELETE = 'SOURCE_DELETE';

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly SourceRepositoryInterface $sourceRepository,
    ) {
    }

    /**
     * @param array{
     *     0: Uuid|null,        # userId
     *     1: string|Source     # subject
     * } $subject
     */
    protected function supports($attribute, $subject): bool
    {
        return is_array($subject)
            && array_key_exists(0, $subject)
            && array_key_exists(1, $subject)
            && ($subject[0] instanceof Uuid || $subject[0] === null)
            && ($subject[1] === Source::class || $subject[1] instanceof Source)
            && (
                (in_array($attribute, [self::LIST, self::CREATE], true) && $subject[1] === Source::class)
                || (in_array(
                    $attribute,
                    [self::GRAB, self::VIEW, self::UPDATE, self::DELETE],
                    true
                ) && $subject[1] instanceof Source)
            );
    }

    /**
     * @param array{
     *     0: Uuid|null,        # userId
     *     1: string|Source     # subject
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
            self::LIST => true,

            self::VIEW,
            self::UPDATE,
            self::GRAB => ($user->hasRole(UserRole::Supervisor->value) || $user->hasRole(UserRole::Admin->value))
                || ($subject instanceof Source
                    && $this->sourceRepository->findOneByIdAndUserId($subject->getId(), $user->getId()) !== null
                    && $user->getId() && $user->getId()->equals($subject->getUser()->getId())
                    && $subject->isActive()
                ),
            default => false,
        };

        //        return match ($attribute) {
        //            self::LIST,
        //            self::CREATE => true,
        //
        //            self::GRAB,
        //            self::VIEW,
        //            self::UPDATE,
        //            self::DELETE => ($user->hasRole(UserRole::Supervisor->value) || $user->hasRole(UserRole::Admin->value))
        //                || ($subject instanceof Source
        //                && $this->sourceRepository->findOneByIdAndUserId($subject->getId(), $user->getId()) !== null
        //                && $user->getId() && $user->getId()->equals($subject->getUser()->getId())
        //                ),
        //
        //            default => false,
        //        };
    }
}
