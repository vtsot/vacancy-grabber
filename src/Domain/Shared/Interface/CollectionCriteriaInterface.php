<?php

declare(strict_types=1);

namespace App\Domain\Shared\Interface;

// todo: remove
interface CollectionCriteriaInterface
{
    public function getOffset(): int;

    public function getLimit(): int;

    public function getSort(): string;

    public function getOrder(): string;

    public function getQuery(): string;
}
