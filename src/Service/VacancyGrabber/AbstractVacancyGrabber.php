<?php

declare(strict_types=1);

namespace App\Service\VacancyGrabber;

use App\Contract\Dictionary\VacancyGrabber;
use App\Contract\Dictionary\VacancyGrabberInterface;
use App\Service\VacancyGrabber\DTO\CompanyDTO;
use App\Service\VacancyGrabber\DTO\VacancyDTO;
use Symfony\Component\DomCrawler\Crawler;

use function preg_replace;
use function trim;

abstract class AbstractVacancyGrabber implements VacancyGrabberInterface
{
    public const VacancyGrabber GRABBER = VacancyGrabber::Dou;

    public function getGrabber(): VacancyGrabber
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
