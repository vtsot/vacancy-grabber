<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Vacancy\Security\Voter;

use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Domain\User\Entity\UserRole;
use App\VacancyGrabber\Domain\User\Repository\UserRepository;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Uid\Uuid;

use function array_key_exists;
use function in_array;
use function is_array;

class VacancyVoter extends Voter
{
    public const string LIST   = 'VACANCY_LIST';
    public const string CREATE = 'VACANCY_CREATE';
    public const string VIEW   = 'VACANCY_VIEW';
    public const string UPDATE = 'VACANCY_UPDATE';
    public const string DELETE = 'VACANCY_DELETE';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly VacancyRepository $vacancyRepository,
    ) {
    }

    /**
     * @param array{
     *     0: Uuid|null,        # userId
     *     1: string|Vacancy    # subject
     * } $subject
     */
    protected function supports($attribute, $subject): bool
    {
        return is_array($subject)
            && array_key_exists(0, $subject)
            && array_key_exists(1, $subject)
            && ($subject[0] instanceof Uuid || $subject[0] === null)
            && ($subject[1] === Vacancy::class || $subject[1] instanceof Vacancy)
            && (
                (in_array($attribute, [self::LIST, self::CREATE], true) && $subject[1] === Vacancy::class)
                || (in_array(
                    $attribute,
                    [self::VIEW, self::UPDATE, self::DELETE],
                    true
                ) && $subject[1] instanceof Vacancy)
            );
    }

    /**
     * @param array{
     *     0: Uuid|null,        # userId
     *     1: string|Vacancy    # subject
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
                || ($subject instanceof Vacancy
                && $this->vacancyRepository->findOneByIdAndUserId($subject->getId(), $user->getId()) !== null
                && $user->getId() && $user->getId()->equals($subject->getUser()->getId())
                ),

            default => false,
        };
    }
}
