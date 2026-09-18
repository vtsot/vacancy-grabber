<?php

declare(strict_types=1);

namespace App\Component\Common\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class AtLeastOneOfPropertyNotNull extends Constraint
{
    public function __construct(
        public array $properties = [],
        public string $message = 'GENERAL.AT_LEAST_ONE_OF_PROPERTY_NOT_NULL',
        public ?bool $replaceError = true,
        public ?string $path = 'additional_message',
        public ?bool $markAll = false,
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function getTargets(): string|array
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
