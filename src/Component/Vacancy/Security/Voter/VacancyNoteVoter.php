<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Security\Voter;

use App\Component\Note\Entity\Note;
use App\Component\Note\Repository\NoteRepository;
use App\Component\User\Contract\Dictionary\UserRole;
use App\Component\User\Repository\UserRepository;
use App\Contract\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Uid\Uuid;

use function array_key_exists;
use function in_array;
use function is_array;

class VacancyNoteVoter extends Voter
{
    public const string LIST   = 'VACANCY_NOTE_LIST';
    public const string CREATE = 'VACANCY_NOTE_CREATE';
    public const string VIEW   = 'VACANCY_NOTE_VIEW';
    public const string UPDATE = 'VACANCY_NOTE_UPDATE';
    public const string DELETE = 'VACANCY_NOTE_DELETE';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly NoteRepository $noteRepository,
    ) {
    }

    /**
     * @param array{
     *     0: Uuid|null,        # userId
     *     1: string|Note    # subject
     * } $subject
     */
    protected function supports($attribute, $subject): bool
    {
        return is_array($subject)
            && array_key_exists(0, $subject)
            && array_key_exists(1, $subject)
            && ($subject[0] instanceof Uuid || $subject[0] === null)
            && ($subject[1] === Note::class || $subject[1] instanceof Note)
            && (
                (in_array($attribute, [self::LIST, self::CREATE], true) && $subject[1] === Note::class)
                || (in_array(
                    $attribute,
                    [self::VIEW, self::UPDATE, self::DELETE],
                    true
                ) && $subject[1] instanceof Note)
            );
    }

    /**
     * @param array{
     *     0: Uuid|null,        # userId
     *     1: string|Note    # subject
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
                || ($subject instanceof Note
                    && $this->noteRepository->findOneByIdAndUserId($subject->getId(), $user->getId()) !== null
                    && $user->getId() && $user->getId()->equals($subject->getUser()->getId())
                ),

            default => false,
        };
    }
}
