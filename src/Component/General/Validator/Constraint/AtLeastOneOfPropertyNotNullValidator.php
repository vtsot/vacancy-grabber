<?php

declare(strict_types=1);

namespace App\Component\General\Validator\Constraint;

use LogicException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function array_map;
use function count;
use function implode;
use function is_array;
use function is_object;
use function sprintf;

class AtLeastOneOfPropertyNotNullValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws ReflectionException
     */
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

        match (true) {
            is_array($value)  => $this->validateValueAsArray($value, $constraint),
            is_object($value) => $this->validateValueAsObject($value, $constraint),
        };
    }

    public function validateValueAsArray(mixed $array, AtLeastOneOfPropertyNotNull $constraint): void
    {
        $errors = [];

        foreach ($constraint->properties as $property) {
            $value = $array[$property] ?? null;

            foreach ($this->validator->validate($value, new NotNull()) as $item) {
                $errors[$property] = $item->getMessage();
            }
        }

        $this->handleErrors($errors, $constraint);
    }

    /**
     * @param object $object
     *
     * @throws ReflectionException
     */
    public function validateValueAsObject(mixed $object, AtLeastOneOfPropertyNotNull $constraint): void
    {
        $errors  = [];
        $reflect = new ReflectionClass($object);

        foreach ($constraint->properties as $property) {
            if ($reflect->hasProperty($property)) {
                $reflectProperty = $reflect->getProperty($property);
                $reflectProperty->setAccessible(true); // only required prior to PHP 8.1.0
                $reflectValue = $reflectProperty->getValue($object);

                foreach ($this->validator->validate($reflectValue, new NotNull()) as $item) {
                    $errors[$property] = $item->getMessage();
                }
            }
        }

        $this->handleErrors($errors, $constraint);
    }

    private function handleErrors(array $errors, AtLeastOneOfPropertyNotNull $constraint): void
    {
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
