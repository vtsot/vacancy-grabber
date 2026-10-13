<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Shared\ControllerRequest;

use App\Contract\ControllerRequest\ControllerRequestInterface;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property array<int, mixed> $fields
 */
abstract readonly class AbstractPatchRequest implements ControllerRequestInterface
{
    public function __construct(
        // the properties to update
        #[Assert\Count(min: 1)]
        #[Assert\All([
            new Assert\Type(type: 'string'),
            new Assert\NotBlank(),
        ])]
        #[SerializedName('properties')]
        public ?array $properties = [],

        #[Assert\Type('array')]
        #[SerializedName('required')]
        public ?array $required = [],

        #[Assert\Type('array')]
        #[SerializedName('modifiers')]
        public ?array $modifiers = [],
        // then each property validate separately
    ) {
    }

    public function __get(string $name): array
    {
        if ($name === 'fields') {
            $fields = [];
            $vars   = get_object_vars($this);
            unset($vars['properties'], $vars['required'], $vars['modifiers']);

            foreach ($vars as $key => $value) {
                $modifier     = $this->modifiers[$key] ?? null;
                $fields[$key] = $modifier && $value ? $modifier($value) : $value;
            }

            return $fields;
        }

        throw new InvalidArgumentException('Undefined property: ' . static::class . "::\$$name");
    }

    public function __set(string $name, mixed $value): void
    {
        if ($name === 'fields') {
            throw new LogicException(
                'Property "fields" is read-only. You must modify individual class properties instead.'
            );
        }
    }

    public function __isset(string $name): bool
    {
        return $name === 'fields';
    }
}
