<?php

declare(strict_types=1);

namespace App\Contract;

use App\VacancyGrabber\Application\Service\SourceGrabber\DTO\CompanyDTO;
use App\VacancyGrabber\Application\Service\SourceGrabber\DTO\VacancyDTO;
use App\VacancyGrabber\Domain\Source\Entity\SourceGrabber;
use Symfony\Component\DomCrawler\Crawler;

interface SourceGrabberInterface
{
    public function getGrabber(): SourceGrabber;

    /** @return array<VacancyDTO> */
    public function grab(string $url): array;

    public function parseCompany(Crawler $node): ?CompanyDTO;

    public function parseVacancy(Crawler $node): VacancyDTO;
}
