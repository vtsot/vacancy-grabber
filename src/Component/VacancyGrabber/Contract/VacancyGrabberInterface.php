<?php

declare(strict_types=1);

namespace App\Component\VacancyGrabber\Contract;

use App\Component\Vacancy\Contract\Dictionary\VacancyGrabber;
use App\Component\VacancyGrabber\DTO\CompanyDTO;
use App\Component\VacancyGrabber\DTO\VacancyDTO;
use Symfony\Component\DomCrawler\Crawler;

interface VacancyGrabberInterface
{
    public function getGrabber(): VacancyGrabber;

    /** @return array<VacancyDTO> */
    public function grab(string $url): array;

    public function parseCompany(Crawler $node): ?CompanyDTO;

    public function parseVacancy(Crawler $node): VacancyDTO;
}
