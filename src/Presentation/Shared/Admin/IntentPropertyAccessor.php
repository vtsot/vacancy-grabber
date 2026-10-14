<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Admin;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

use function is_object;
use function str_contains;

/**
 * Lets generic tooling (Symfony Form, Sonata Admin) edit setter-less entities: single-segment writes
 * listed in EntityFieldMap go through intent methods; everything else uses the regular accessor.
 */
#[AsDecorator('property_accessor')]
final readonly class IntentPropertyAccessor implements PropertyAccessorInterface
{
    public function __construct(
        #[AutowireDecorated]
        private PropertyAccessorInterface $inner,
    ) {
    }

    public function setValue(object|array &$objectOrArray, string|PropertyPathInterface $propertyPath, mixed $value): void
    {
        if (is_object($objectOrArray)
            && ($field = $this->field($propertyPath))
            && ($writer = EntityFieldMap::writer($objectOrArray, $field))
        ) {
            $writer($objectOrArray, $value);

            return;
        }

        $this->inner->setValue($objectOrArray, $propertyPath, $value);
    }

    public function getValue(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): mixed
    {
        if (is_object($objectOrArray)
            && ($field = $this->field($propertyPath))
            && ($reader = EntityFieldMap::reader($objectOrArray, $field))
        ) {
            return $reader($objectOrArray);
        }

        return $this->inner->getValue($objectOrArray, $propertyPath);
    }

    public function isWritable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        if (is_object($objectOrArray)
            && ($field = $this->field($propertyPath))
            && EntityFieldMap::writer($objectOrArray, $field)
        ) {
            return true;
        }

        return $this->inner->isWritable($objectOrArray, $propertyPath);
    }

    public function isReadable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        return $this->inner->isReadable($objectOrArray, $propertyPath);
    }

    private function field(string|PropertyPathInterface $propertyPath): ?string
    {
        $path = (string) $propertyPath;

        return $path !== '' && !str_contains($path, '.') && !str_contains($path, '[') ? $path : null;
    }
}
