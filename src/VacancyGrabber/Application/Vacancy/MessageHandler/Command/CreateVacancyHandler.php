<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\MessageHandler\Command;

use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Exception\Validation\ValidationFailedException;
use App\VacancyGrabber\Application\Security\Voter\VacancyVoter;
use App\VacancyGrabber\Application\Shared\Bus\CommandHandlerInterface;
use App\VacancyGrabber\Application\Vacancy\DTO\VacancyDetailsDTO;
use App\VacancyGrabber\Application\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\VacancyGrabber\Application\Vacancy\EntityFactory\VacancyEntityFactory;
use App\VacancyGrabber\Application\Vacancy\Message\Command\CreateVacancyCommand;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CreateVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
        private VacancyEntityFactory $vacancyEntityFactory,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    /**
     * @throws ValidationFailedException
     */
    public function __invoke(CreateVacancyCommand $command): VacancyDetailsDTO
    {
        if (!$this->security->isGranted(VacancyVoter::CREATE, [$command->userId, Vacancy::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $vacancy = $this->vacancyEntityFactory->processCommand($command);
        $this->vacancyRepository->save($vacancy);

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
