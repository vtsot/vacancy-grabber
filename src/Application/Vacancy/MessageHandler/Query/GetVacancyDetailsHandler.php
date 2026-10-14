<?php

declare(strict_types=1);

namespace App\Application\Vacancy\MessageHandler\Query;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Security\Voter\VacancyVoter;
use App\Application\Shared\Bus\QueryHandlerInterface;
use App\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\Application\Vacancy\DTO\VacancyDetailsDTO;
use App\Application\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Application\Vacancy\Message\Query\GetVacancyDetailsQuery;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Vacancy\Repository\VacancyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetVacancyDetailsHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
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
