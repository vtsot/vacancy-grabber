<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait SalaryEntityTrait
{
    #[ORM\Column(name: 'salary', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $salary = null;

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(?string $salary): static
    {
        $this->salary = $salary;

        return $this;
    }
}
