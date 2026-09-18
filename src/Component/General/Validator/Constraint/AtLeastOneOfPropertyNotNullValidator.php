<?php

declare(strict_types=1);

namespace App\Component\General\Validator\Constraint;

use LogicException;
use ReflectionClass;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function array_map;
use function count;
use function implode;
use function is_object;
use function sprintf;

class AtLeastOneOfPropertyNotNullValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AtLeastOneOfPropertyNotNull) {
            throw new LogicException(sprintf(
                'You can only pass "%s" constraint to this validator.', AtLeastOneOfPropertyNotNull::class
            ));
        }

        if (!$constraint->properties) {
            throw new MissingOptionsException(sprintf(
                'The parameter "%s" is required for the validator "%s".',
                'properties', self::class,
            ), ['properties']);
        }

        // nothing to validate
        if (!is_object($value)) {
            return;
        }

        /** @var object $object */
        $object  = $value;
        $errors  = [];
        $reflect = new ReflectionClass($object);

        foreach ($constraint->properties as $property) {
            if ($reflect->hasProperty($property)) {
                $reflectProperty = $reflect->getProperty($property);
                $reflectProperty->setAccessible(true); // only required prior to PHP 8.1.0
                $reflectValue = $reflectProperty->getValue($value);

                foreach ($this->validator->validate($reflectValue, new NotNull()) as $item) {
                    $errors[$property] = $item->getMessage();
                }
            }
        }

        $countErrors     = count($errors);
        $countProperties = count($constraint->properties);

        if ($countErrors === $countProperties) {
            $parameterValue = implode(', ', array_map(static fn (string $item) => sprintf('"%s"', $item), $constraint->properties));
            $propertyPaths  = $constraint->markAll
                ? $constraint->properties
                : [$constraint->path ?? ''];


            foreach ($propertyPaths as $property) {
                $builder = $this->context
                    ->buildViolation($constraint->message)
                    ->atPath($property)
                    ->setParameter('%properties%', $parameterValue);

                if ($constraint->replaceError) {
                    $builder->setCode('REPLACE_ERROR_MESSAGE');
                }

                $builder->addViolation();
            }
        }
    }
}
