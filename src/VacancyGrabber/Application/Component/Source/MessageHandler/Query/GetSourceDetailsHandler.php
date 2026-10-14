<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Source\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Component\Source\DTO\SourceDetailsDTO;
use App\VacancyGrabber\Application\Component\Source\DTOFactory\SourceDetailsDTOFactory;
use App\VacancyGrabber\Application\Component\Source\Message\Query\GetSourceDetailsQuery;
use App\VacancyGrabber\Application\Component\Source\Security\Voter\SourceVoter;
use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Exception\Validation\EntityNotFoundException;
use App\VacancyGrabber\Application\Message\Query\Traits\FiltersOrderByTrait;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetSourceDetailsHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
        private SourceDetailsDTOFactory $sourceDetailsDTOFactory,
    ) {
    }

    public function __invoke(GetSourceDetailsQuery $query): SourceDetailsDTO
    {
        if (($source = $this->sourceRepository->findOneById($query->sourceId)) === null) {
            throw new EntityNotFoundException($this->translator->trans(id: 'GENERAL.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(SourceVoter::VIEW, [$query->userId, $source])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
