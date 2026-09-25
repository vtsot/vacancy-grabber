<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Command;

use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Component\Vacancy\Entity\Vacancy;
use App\Component\Vacancy\Message\Command\CreateVacancyCommand;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CreateVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    public function __invoke(CreateVacancyCommand $command): VacancyDetailsDTO
    {
        $vacancy = new Vacancy();
        //            ->setName($command->name)
        //            ->setUrl($command->url)
        //            ->setActive((bool) $command->active);

        $this->em->persist($vacancy);
        $this->em->flush();

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
