<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Query;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Component\Vacancy\Message\Query\GetVacancyDetailsQuery;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Contract\MessageHandler\QueryHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetVacancyDetailsHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    public function __invoke(GetVacancyDetailsQuery $query): VacancyDetailsDTO
    {
        if (($vacancy = $this->vacancyRepository->findOneById($query->id)) === null) {
            throw new EntityNotFoundException($this->translator->trans(id: 'GENERAL.NOT_FOUND', domain: 'validators'));
        }

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
