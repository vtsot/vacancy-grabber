<?php

declare(strict_types=1);

namespace App\Component\VacancyGrabber\Service;

use App\Component\VacancyGrabber\DTO\CompanyDTO;
use App\Component\VacancyGrabber\DTO\VacancyDTO;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyGrabber;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function array_last;
use function explode;
use function max;
use function sleep;

class DjinniVacancyGrabber extends AbstractVacancyGrabber
{
    public const VacancyGrabber GRABBER = VacancyGrabber::Djinni;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    private function resolveMaxPage(Crawler $node): int
    {
        $maxPage    = 1;
        $pagination = $node->filter('#jobs_main ul.pagination');

        if ($pagination->count() > 0 && $pagination->filter('a.page-link')->count() > 0) {
            $pages   = $pagination->filter('a.page-link')->each(fn (Crawler $node) => (int) $node->text());
            $maxPage = (int) max($pages);
        }

        return $maxPage ?: 1;
    }

    public function grabPage(string $url, int $page): array
    {
        $url .= "&page=$page";
        $response = $this->httpClient->request('GET', $url);
        $crawler  = new Crawler($response->getContent());

        return $crawler->filter('div.job-item')->each(fn ($node) => $this->parseVacancy($node));
    }

    public function grab(string $url): array
    {
        $response = $this->httpClient->request('GET', $url);
        $crawler  = new Crawler($response->getContent());
        $maxPage  = $this->resolveMaxPage($crawler);
        $result   = [];
        sleep(1);

        // grab each page
        for ($page = 1; $page <= $maxPage; $page++) {
            $result = [...$result, ...$this->grabPage($url, $page)];
            sleep(1);
        }

        return $result;
    }

    public function parseCompany(Crawler $node): ?CompanyDTO
    {
        $name = $node->filter('h2.job-item__position')->nextAll()->first()->filter('span')->getNode(0)?->nodeValue;

        return new CompanyDTO(
            url: null,
            name: $this->cleanText($name),
        );
    }

    public function parseVacancy(Crawler $node): VacancyDTO
    {
        $vacancyUrl  = 'https://djinni.co' . $node->filter('a.job_item__header-link')->attr('href');
        $externalId  = array_last(explode('-', $node->attr('id')));
        $title       = $this->cleanText($node->filter('h2.job-item__position')->getNode(0)?->nodeValue);
        $description = $this->cleanText(
            $node->filter("div[id=job-description-{$externalId}]")->filter('.js-truncated-text')->html()
        );
        $salary  = $this->cleanText($node->filter('a.job_item__header-link')->filter('div.fs-5')->text());
        $company = $this->parseCompany($node);

        return new VacancyDTO(
            externalId: $externalId,
            url: $vacancyUrl,
            title: $title,
            description: $description,
            salary: $salary,
            company: $company,
        );
    }
}
