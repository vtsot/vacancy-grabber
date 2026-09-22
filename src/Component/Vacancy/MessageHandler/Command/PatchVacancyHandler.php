<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Command;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Exception\Validation\UnprocessableEntityException;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Component\Vacancy\Message\Command\PatchVacancyCommand;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function ucfirst;

final readonly class PatchVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    public function __invoke(PatchVacancyCommand $command): VacancyDetailsDTO
    {
        if (!$vacancy = $this->vacancyRepository->findOneById($command->id)) {
            throw new EntityNotFoundException($this->translator->trans('VACANCY.NOT_FOUND'));
        }

        if ($command->name === null && $command->url === null && $command->active === null) {
            throw new UnprocessableEntityException($this->translator->trans(
                'GENERAL.AT_LEAST_ONE_OF_PROPERTY_NOT_NULL',
                ['%properties%' => '"name", "url", "active"'],
                'validators'
            ));
        }

        foreach (['name', 'url', 'active'] as $prop) {
            if ($command->$prop !== null) {
                $vacancy->{'set' . ucfirst($prop)}($command->$prop);
                $doSave = true;
            }
        }

        if ($doSave ?? false) {
            $this->em->flush();
        }

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
