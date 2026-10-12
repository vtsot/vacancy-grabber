<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Company\Security\Voter;

use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Company\Repository\CompanyRepositoryInterface;
use App\VacancyGrabber\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Uid\Uuid;

use function array_key_exists;
use function in_array;
use function is_array;

class CompanyVoter extends Voter
{
    public const string LIST   = 'COMPANY_LIST';
    public const string CREATE = 'COMPANY_CREATE';
    public const string VIEW   = 'COMPANY_VIEW';
    public const string UPDATE = 'COMPANY_UPDATE';
    public const string DELETE = 'COMPANY_DELETE';

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly CompanyRepositoryInterface $companyRepository,
    ) {
    }

    /**
     * @param array{
     *     0: Uuid|null,        # userId
     *     1: string|Company    # subject
     * } $subject
     */
    protected function supports($attribute, $subject): bool
    {
        return is_array($subject)
            && array_key_exists(0, $subject)
            && array_key_exists(1, $subject)
            && ($subject[0] instanceof Uuid || $subject[0] === null)
            && ($subject[1] === Company::class || $subject[1] instanceof Company)
            && (
                (in_array($attribute, [self::LIST, self::CREATE], true) && $subject[1] === Company::class)
                || (in_array(
                    $attribute,
                    [self::VIEW, self::UPDATE, self::DELETE],
                    true
                ) && $subject[1] instanceof Company)
            );
    }

    /**
     * @param array{
     *     0: Uuid|null,        # userId
     *     1: string|Company    # subject
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
            default    => false,
        };
    }
}
