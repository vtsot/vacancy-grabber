<?php

declare(strict_types=1);

namespace App\Contract\Dictionary;

use App\Service\VacancyGrabber\VacancyDTO;
use Symfony\Component\DomCrawler\Crawler;

interface VacancyGrabberInterface
{
    public function getGrabber(): VacancyGrabber;

    /** @return array<VacancyDTO> */
    public function grab(string $url): array;

    public function parseVacancy(Crawler $node): VacancyDTO;
}
