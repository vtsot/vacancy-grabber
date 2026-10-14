<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class EnumValues extends Constraint
{
    public function __construct(
        public string $enumClass,
        public string $message = '"%value%" does not exists in enum "%enumClass%".',
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }
}
