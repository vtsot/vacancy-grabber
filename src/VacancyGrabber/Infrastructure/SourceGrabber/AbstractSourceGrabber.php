<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\SourceGrabber;

use App\VacancyGrabber\Domain\Source\Entity\SourceGrabber;
use App\VacancyGrabber\Domain\Source\Grabber\GrabbedCompany;
use App\VacancyGrabber\Domain\Source\Grabber\GrabbedVacancy;
use App\VacancyGrabber\Domain\Source\Grabber\Grabber;
use Symfony\Component\DomCrawler\Crawler;

use function preg_replace;
use function trim;

abstract class AbstractSourceGrabber implements Grabber
{
    public const SourceGrabber GRABBER = SourceGrabber::Dou;

    public function getGrabber(): SourceGrabber
    {
        return static::GRABBER;
    }

    abstract public function grab(string $url): array;

    abstract public function parseCompany(Crawler $node): ?GrabbedCompany;

    abstract public function parseVacancy(Crawler $node): GrabbedVacancy;

    protected function cleanText(?string $text): ?string
    {
        $text ??= '';
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text) ?: null;
    }
}
