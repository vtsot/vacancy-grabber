<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Shared\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Shared\Exception\Validation\EntityNotFoundException;
use App\VacancyGrabber\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\VacancyGrabber\Application\Vacancy\DTO\VacancyDetailsDTO;
use App\VacancyGrabber\Application\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\VacancyGrabber\Application\Vacancy\Message\Query\GetVacancyDetailsQuery;
use App\VacancyGrabber\Application\Vacancy\Security\Voter\VacancyVoter;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetVacancyDetailsHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private VacancyRepositoryInterface $vacancyRepository,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    public function __invoke(GetVacancyDetailsQuery $query): VacancyDetailsDTO
    {
        if (!($vacancy = $this->vacancyRepository->findOneById($query->vacancyId))) {
            throw new EntityNotFoundException($this->translator->trans(id: 'VACANCY.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(VacancyVoter::VIEW, [$query->userId, $vacancy])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
