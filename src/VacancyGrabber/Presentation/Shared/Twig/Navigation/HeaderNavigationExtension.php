<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Shared\Twig\Navigation;

use App\VacancyGrabber\Application\Shared\DTO\Navigation\NavigationDTO;
use Twig\TwigFunction;

class HeaderNavigationExtension extends AbstractNavigationExtension
{
    protected const array NAVIGATION = [
        [
            'key'   => 'web.dashboard',
            'route' => 'web.dashboard',
            'title' => 'NAVIGATION.ITEM.DASHBOARD',
        ],
        [
            'key'   => 'vacancy-group',
            'route' => null,
            'title' => 'NAVIGATION.GROUP.VACANCY',
            'items' => [
                ['key' => 'web.source', 'route' => 'web.source', 'title' => 'NAVIGATION.ITEM.SOURCE'],
                ['key' => 'web.grab-log', 'route' => 'web.grab-log', 'title' => 'NAVIGATION.ITEM.GRAB_LOG'],
                ['key'       => 'web.vacancy',
                    'route'  => 'web.vacancy',
                    'title'  => 'NAVIGATION.ITEM.VACANCY',
                    'params' => ['status' => 'pending'],
                ],
            ],
        ],
        [
            'key'   => 'company-group',
            'route' => null,
            'title' => 'NAVIGATION.GROUP.COMPANY',
            'items' => [
                ['key' => 'web.company', 'route' => 'web.company', 'title' => 'NAVIGATION.ITEM.COMPANY'],
                ['key' => 'web.note', 'route' => 'web.note', 'title' => 'NAVIGATION.ITEM.NOTE'],
            ],
        ],
    ];

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getHeaderNavigationItems', [$this, 'getHeaderNavigationItems']),
            new TwigFunction('getHeaderNavigationOpened', [$this, 'getHeaderNavigationOpened']),
        ];
    }

    /**
     * @return array<int, NavigationDTO>
     */
    public function getHeaderNavigationItems(): array
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        return $user
            ? $this->resolveNavigationWithSubItems(self::NAVIGATION)
            : [];
    }

    /**
     * @return array<int, string>
     */
    public function getHeaderNavigationOpened(): array
    {
        $opened = [];
        $items  = self::NAVIGATION;
        $this->resolveNavigationOpenedParentKeys($opened, null, $items);

        return $opened;
    }
}
