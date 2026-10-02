<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Command;

use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\General\Exception\Validation\ValidationFailedException;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Component\Vacancy\EntityFactory\VacancyEntityFactory;
use App\Component\Vacancy\Message\Command\UpdateVacancyCommand;
use App\Component\Vacancy\Security\Voter\VacancyVoter;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class UpdateVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
        private VacancyEntityFactory $vacancyEntityFactory,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    /**
     * @throws ORMException
     * @throws ValidationFailedException
     */
    public function __invoke(UpdateVacancyCommand $command): VacancyDetailsDTO
    {
        $vacancy = $this->vacancyEntityFactory->processCommand($command);

        if (!$this->security->isGranted(VacancyVoter::UPDATE, [$command->userId, $vacancy])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $this->em->flush();

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
