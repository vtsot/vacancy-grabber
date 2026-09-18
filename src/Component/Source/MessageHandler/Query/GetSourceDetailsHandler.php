<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Query;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Component\Source\DTO\SourceDetailsDTO;
use App\Component\Source\DTOFactory\SourceDetailsDTOFactory;
use App\Component\Source\Message\Query\GetSourceDetailsQuery;
use App\Component\Source\Repository\SourceRepository;
use App\Contract\MessageHandler\QueryHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetSourceDetailsHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
        private SourceDetailsDTOFactory $sourceDetailsDTOFactory,
    ) {
    }

    public function __invoke(GetSourceDetailsQuery $query): SourceDetailsDTO
    {
        if (($source = $this->sourceRepository->findOneById($query->id)) === null) {
            throw new EntityNotFoundException($this->translator->trans(id: 'GENERAL.NOT_FOUND', domain: 'validators'));
        }

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
