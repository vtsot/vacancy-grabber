<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Source\Grabber;

use App\VacancyGrabber\Domain\Shared\Exception\InvalidArgumentException;
use App\VacancyGrabber\Domain\Source\Entity\Source;

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
