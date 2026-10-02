<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Query;

use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Component\Source\DTO\SourceDetailsDTO;
use App\Component\Source\DTOFactory\SourceDetailsDTOFactory;
use App\Component\Source\Message\Query\GetSourceDetailsQuery;
use App\Component\Source\Repository\SourceRepository;
use App\Component\Source\Security\Voter\SourceVoter;
use App\Contract\MessageHandler\QueryHandlerInterface;
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
