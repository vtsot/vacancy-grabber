<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Command;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Component\Vacancy\Message\Command\UpdateVacancyCommand;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class UpdateVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    public function __invoke(UpdateVacancyCommand $command): VacancyDetailsDTO
    {
        if (!$vacancy = $this->vacancyRepository->findOneById($command->id)) {
            throw new EntityNotFoundException($this->translator->trans('VACANCY.NOT_FOUND'));
        }

        $vacancy
//            ->setName($command->name)
//            ->setUrl($command->url)
//            ->setActive((bool) $command->active);
;
        $this->em->flush();

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
