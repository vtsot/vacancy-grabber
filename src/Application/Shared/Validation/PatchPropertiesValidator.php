<?php

declare(strict_types=1);

namespace App\Application\Shared\Validation;

use App\Application\Exception\Validation\ValidationFailedException;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_key_exists;
use function Symfony\Component\String\u;

final readonly class PatchPropertiesValidator
{
    public function __construct(
        private ValidatorInterface $validator,
        private TranslatorInterface $translator,
    ) {
    }

    public function validate(
        PatchPropertiesInterface $patchProperties,
    ): ?ValidationFailedException {
        $violations = [];

        // properties field is  required
        if (($errors = $this->validator->validate($patchProperties->properties, new Count(min: 1)))
            && $errors->count()
        ) {
            $violations[] = [
                'message' => $errors->get(0)->getMessage(),
                'path'    => 'properties',
            ];
        }

        // do validate required fields
        // the field pointed in the properties should exist in fields
        foreach ($patchProperties->properties as $property) {
            $property = u($property)->camel()->toString();

            if (!array_key_exists($property, $patchProperties->fields)) {
                $violations[] = [
                    'message' => $this->translator->trans(id: 'GENERAL.PROPERTY_REQUIRED', domain: 'validators'),
                    'path'    => $property,
                ];
            }
        }

        // validate each property
        foreach ($patchProperties->required as $index => $property) {
            if (($errors = $this->validator->validate($patchProperties->fields[$property] ?? null, new NotBlank()))
                && $errors->count()
            ) {
                $violations[] = [
                    'message' => $errors->get(0)->getMessage(),
                    'path'    => $property,
                ];
            }
        }

        return $violations
            ? ValidationFailedException::fromArray(
                $violations,
                $this->translator->trans(id: 'GENERAL.VALIDATION_FAILED', domain: 'validators')
            )
            : null;
    }
}
