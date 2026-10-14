<?php

declare(strict_types=1);

namespace App\Application\Vacancy\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Exception\Validation\ValidationFailedException;
use App\Application\Security\Voter\VacancyVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Vacancy\DTO\VacancyDetailsDTO;
use App\Application\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Application\Vacancy\EntityFactory\VacancyEntityFactory;
use App\Application\Vacancy\Message\Command\CreateVacancyCommand;
use App\Domain\Vacancy\Entity\Vacancy;
use App\Domain\Vacancy\Repository\VacancyRepository;
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
