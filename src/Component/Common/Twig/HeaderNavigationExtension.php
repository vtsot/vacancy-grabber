<?php

declare(strict_types=1);

namespace App\Component\Common\Twig;

use App\DTO\HeaderNavigation\HeaderNavigationDTO;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HeaderNavigationExtension extends AbstractExtension
{
    private const array NAVIGATION = [
        ['route' => 'web.source', 'title' => 'Sources'],
        ['route' => 'web.grab-log', 'title' => 'Grab Logs'],
        ['route' => 'web.company', 'title' => 'Companies'],
        ['route' => 'web.vacancy', 'title' => 'Vacancies'],
        ['route' => 'web.note', 'title' => 'Notes'],
    ];

    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getHeaderNavigation', [$this, 'getHeaderNavigation']),
        ];
    }

    /**
     * @return array<int, HeaderNavigationDTO>
     */
    public function getHeaderNavigation(): array
    {
        $items = [];

        foreach (self::NAVIGATION as $item) {
            $items[] = new HeaderNavigationDTO(
                key: $item['route'],
                title: $item['title'],
                url: $this->router->generate($item['route'])
            );
        }

        return $items;
    }
}
