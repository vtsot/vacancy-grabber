<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class EntitiesExists extends Constraint
{
    public function __construct(
        public string $entity,
        public string $message = 'Entities "%entity%" with property "%property%": "%values%" does not exist.',
        public string $property = 'id',
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }
}
