<?php

declare(strict_types=1);

namespace App\Contract\Dictionary;

use App\Service\VacancyGrabber\DTO\CompanyDTO;
use App\Service\VacancyGrabber\DTO\VacancyDTO;
use Symfony\Component\DomCrawler\Crawler;

interface VacancyGrabberInterface
{
    public function getGrabber(): VacancyGrabber;

    /** @return array<VacancyDTO> */
    public function grab(string $url): array;

    public function parseCompany(Crawler $node): ?CompanyDTO;

    public function parseVacancy(Crawler $node): VacancyDTO;
}
