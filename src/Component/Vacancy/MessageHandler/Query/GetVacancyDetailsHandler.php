<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Query;

use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Component\Vacancy\Message\Query\GetVacancyDetailsQuery;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Component\Vacancy\Security\Voter\VacancyVoter;
use App\Contract\MessageHandler\QueryHandlerInterface;
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
