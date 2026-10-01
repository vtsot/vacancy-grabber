<?php

declare(strict_types=1);

namespace App\Component\VacancyGrabber\Service;

use App\Component\Vacancy\Contract\Dictionary\VacancyGrabber;
use App\Component\VacancyGrabber\DTO\CompanyDTO;
use App\Component\VacancyGrabber\DTO\VacancyDTO;
use JsonException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function array_filter;
use function end;
use function explode;
use function json_decode;
use function parse_url;
use function sleep;

use const JSON_THROW_ON_ERROR;
use const PHP_URL_PATH;
use const PHP_URL_QUERY;

class DouVacancyGrabber extends AbstractVacancyGrabber
{
    public const VacancyGrabber GRABBER  = VacancyGrabber::Dou;
    public const string         XHR_LOAD = 'https://jobs.dou.ua/vacancies/xhr-load/';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    private function resolveCsrfToken(Crawler $crawler): ?string
    {
        $csrfToken = null;
        $crawler->filter('script')->each(function (Crawler $node) use (&$csrfToken) {
            $content = $node->text();

            if (preg_match('/window\.CSRF_TOKEN\s*=\s*"([^"]+)"/', $content, $matches)) {
                $csrfToken = $matches[1];
            }
        });

        return $csrfToken;
    }

    /**
     * @return array<VacancyDTO>
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface|JsonException
     */
    public function grab(string $url): array
    {
        $response  = $this->httpClient->request('GET', $url);
        $crawler   = new Crawler($response->getContent());
        $csrfToken = $this->resolveCsrfToken($crawler);
        $cookies   = $response->getHeaders()['set-cookie'] ?? null;
        sleep(1);

        // grab
        $result  = $crawler->filter('#vacancyListId li.l-vacancy')->each(fn ($node) => $this->parseVacancy($node));
        $moreBtn = $crawler->filter('#vacancyListId div.more-btn')->getNode(0)?->nodeValue;

        // grab more
        if ($moreBtn !== null
            && $csrfToken !== null
            && ($more = $this->grabMore($url, $csrfToken, $cookies))
        ) {
            $result = [...$result, ...$more];
        }

        return $result;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    private function grabMore(string $url, string $csrfToken, array $cookies, int $count = 20): array
    {
        $xhrUrl   = self::XHR_LOAD . '?' . parse_url($url, PHP_URL_QUERY);
        $response = $this->httpClient->request('POST', $xhrUrl, [
            'headers' => [
                'Referer' => $url,
                'Cookie'  => implode('; ', $cookies),
            ],
            'body' => [
                'csrfmiddlewaretoken' => $csrfToken,
                'count'               => $count,
            ],
        ]);

        $data    = json_decode($response->getContent(false), true, 512, JSON_THROW_ON_ERROR);
        $crawler = new Crawler($data['html'] ?? null);
        $result  = $crawler->filter('li.l-vacancy')->each(fn (Crawler $node) => $this->parseVacancy($node));
        $last    = ($data['last'] ?? null) ?: false;
        $count += ($data['num'] ?? 0);
        sleep(1);

        if (!$last
            && ($more = $this->grabMore($url, $csrfToken, $cookies, $count))
        ) {
            $result = [...$result, ...$more];
        }

        return $result;
    }

    public function parseVacancy(Crawler $node): VacancyDTO
    {
        $vacancyUrl  = $node->filter('div.title a.vt')->attr('href');
        $path        = parse_url($vacancyUrl, PHP_URL_PATH);
        $segments    = array_filter(explode('/', $path));
        $externalId  = end($segments);
        $title       = $this->cleanText($node->filter('div.title')->getNode(0)?->nodeValue);
        $description = $this->cleanText($node->filter('div.sh-info')->getNode(0)?->nodeValue);
        $salary      = $this->cleanText($node->filter('span.salary')->getNode(0)?->nodeValue);
        $company     = $this->parseCompany($node);

        return new VacancyDTO(
            externalId: $externalId,
            url: $vacancyUrl,
            title: $title,
            description: $description,
            salary: $salary,
            company: $company,
        );
    }

    public function parseCompany(Crawler $node): ?CompanyDTO
    {
        $companyUrl = $node->filter('div.title a.company')->attr('href');
        $name       = $this->cleanText($node->filter('div.title a.company')->getNode(0)?->nodeValue);

        return new CompanyDTO(
            url: $companyUrl,
            name: $name,
        );
    }
}
