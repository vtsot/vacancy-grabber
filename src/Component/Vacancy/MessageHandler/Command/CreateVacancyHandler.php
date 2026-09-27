<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Command;

use App\Component\General\Exception\Validation\ValidationFailedException;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Component\Vacancy\EntityFactory\VacancyEntityFactory;
use App\Component\Vacancy\Message\Command\CreateVacancyCommand;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

final readonly class CreateVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private VacancyEntityFactory $vacancyEntityFactory,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    /**
     * @throws ORMException
     * @throws ValidationFailedException
     */
    public function __invoke(CreateVacancyCommand $command): VacancyDetailsDTO
    {
        $vacancy = $this->vacancyEntityFactory->processCommand($command);
        $this->em->persist($vacancy);
        $this->em->flush();

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
