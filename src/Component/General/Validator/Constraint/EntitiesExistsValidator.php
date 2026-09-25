<?php

declare(strict_types=1);

namespace App\Component\General\Validator\Constraint;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_diff;
use function array_map;
use function count;
use function implode;
use function sprintf;
use function Symfony\Component\String\s;
use function ucfirst;

final class EntitiesExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EntitiesExists) {
            throw new LogicException(sprintf('You can only pass %s constraint to this validator.', EntitiesExists::class));
        }

        if ($value === null || $value === '') {
            return;
        }

        if ($constraint->entity === '') {
            throw new LogicException(sprintf('Must set "entity" on "%s" validator', EntitiesExists::class));
        }

        $data = $this->entityManager->getRepository($constraint->entity)->findBy([
            $constraint->property => $value,
        ]);

        $dataPropertyValues = array_map(static fn (object $entity): mixed => $entity->{'get' . ucfirst(s($constraint->property)->camel()->toString())}(), $data);

        $diff = array_diff($value, $dataPropertyValues);

        if (count($diff) > 0) {
            $this->context
                ->buildViolation($this->translator->trans($constraint->message))
                ->setParameter('%entity%', $constraint->entity)
                ->setParameter('%property%', $constraint->property)
                ->setParameter('%values%', implode(', ', $diff))
                ->addViolation();
        }
    }
}
