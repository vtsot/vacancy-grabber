<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Source\Grabber;

use App\VacancyGrabber\Domain\Source\Entity\SourceGrabber;

/**
 * Port: reads vacancies from an external job site.
 */
interface Grabber
{
    public function getGrabber(): SourceGrabber;

    /** @return list<GrabbedVacancy> */
    public function grab(string $url): array;
}
