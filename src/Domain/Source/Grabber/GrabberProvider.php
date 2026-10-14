<?php

declare(strict_types=1);

namespace App\Domain\Source\Grabber;

use App\Domain\Shared\Exception\InvalidArgumentException;
use App\Domain\Source\Entity\Source;

/**
 * Port: picks the grabber that can read the given source.
 */
interface GrabberProvider
{
    /**
     * @throws InvalidArgumentException when no grabber supports the source
     */
    public function forSource(Source $source): Grabber;
}
