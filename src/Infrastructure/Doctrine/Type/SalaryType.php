<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\Vacancy\ValueObject\Salary;

/**
 * @extends StringValueObjectType<Salary>
 */
final class SalaryType extends StringValueObjectType
{
    public const string NAME = 'salary';

    protected function valueObjectClass(): string
    {
        return Salary::class;
    }
}
