<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Admin;

use App\Domain\Shared\Exception\DomainException;
use ArrayIterator;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormError;
use Traversable;

/**
 * Writes each field separately so that a domain rule rejecting one value (e.g. an invalid URL)
 * shows up as an error on that field instead of failing the request.
 */
final readonly class DomainAwareDataMapper implements DataMapperInterface
{
    public function __construct(
        private DataMapperInterface $inner,
    ) {
    }

    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        $this->inner->mapDataToForms($viewData, $forms);
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        foreach ($forms as $form) {
            try {
                $this->inner->mapFormsToData(new ArrayIterator([$form]), $viewData);
            } catch (DomainException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }
    }
}
