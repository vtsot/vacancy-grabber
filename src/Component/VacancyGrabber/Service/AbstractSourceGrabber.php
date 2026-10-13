<?php

declare(strict_types=1);

namespace App\Component\VacancyGrabber\Service;

use App\Component\VacancyGrabber\Contract\VacancyGrabberInterface;
use App\Component\VacancyGrabber\DTO\CompanyDTO;
use App\Component\VacancyGrabber\DTO\VacancyDTO;
use App\VacancyGrabber\Domain\Source\Entity\SourceGrabber;
use Symfony\Component\DomCrawler\Crawler;

use function preg_replace;
use function trim;

abstract class AbstractSourceGrabber implements VacancyGrabberInterface
{
    public const SourceGrabber GRABBER = SourceGrabber::Dou;

    public function getGrabber(): SourceGrabber
    {
        return static::GRABBER;
    }

    abstract public function grab(string $url): array;

    abstract public function parseCompany(Crawler $node): ?CompanyDTO;

    abstract public function parseVacancy(Crawler $node): VacancyDTO;

    protected function cleanText(?string $text): ?string
    {
        $text ??= '';
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text) ?: null;
    }
}
