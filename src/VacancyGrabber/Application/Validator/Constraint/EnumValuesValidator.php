<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Validator\Constraint;

use LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

use function class_exists;
use function in_array;
use function sprintf;

final class EnumValuesValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EnumValues) {
            throw new LogicException(sprintf('You can only pass %s constraint to this validator.', EnumValues::class));
        }

        if ($constraint->enumClass === '') {
            throw new LogicException(sprintf('"enumCLass" must be set on "%s" validator', EnumValues::class));
        }

        if (!class_exists($constraint->enumClass)) {
            throw new LogicException(sprintf('Enum "%s" does not exists', $constraint->enumClass));
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!in_array($value, $constraint->enumClass::values(), true)) {
            $this->context
                ->buildViolation($this->translator->trans($constraint->message))
                ->setParameter('%enumClass%', $constraint->enumClass)
                ->setParameter('%value%', (string) $value)
                ->addViolation();
        }
    }
}
