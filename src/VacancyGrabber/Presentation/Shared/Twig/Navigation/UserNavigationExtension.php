<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Shared\Twig\Navigation;

use App\VacancyGrabber\Application\Shared\DTO\Navigation\NavigationDTO;
use Twig\TwigFunction;

class UserNavigationExtension extends AbstractNavigationExtension
{
    private const array LOGGED_IN_USER_MENU = [
        [
            'key'         => 'web.auth.profile',
            'route'       => null/* 'web.auth.profile' */,
            'title'       => 'NAVIGATION.USER.PROFILE',
            'prependIcon' => 'mdi-account',
        ],
        [
            'key'         => 'web.auth.logout',
            'route'       => 'web.auth.logout',
            'title'       => 'NAVIGATION.USER.LOGOUT',
            'prependIcon' => 'mdi-account-arrow-right',
        ],
    ];

    private const array UNDEFINED_USER_MENU = [
        [
            'key'         => 'web.auth.login',
            'route'       => 'web.auth.login',
            'title'       => 'NAVIGATION.USER.LOGIN',
            'prependIcon' => 'mdi-account-arrow-left',
        ],
    ];

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getUserNavigationItems', [$this, 'getUserNavigationItems']),
            new TwigFunction('getUserNavigationOpened', [$this, 'getUserNavigationOpened']),
        ];
    }

    /**
     * @return array<int, NavigationDTO>
     */
    public function getUserNavigationItems(): array
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        return $user
            ? $this->resolveNavigationWithSubItems(self::LOGGED_IN_USER_MENU)
            : $this->resolveNavigationWithSubItems(self::UNDEFINED_USER_MENU);
    }

    /**
     * @return array<int, string>
     */
    public function getUserNavigationOpened(): array
    {
        $opened = [];
        $user   = $this->tokenStorage->getToken()?->getUser();
        $items  = $user ? self::LOGGED_IN_USER_MENU : self::UNDEFINED_USER_MENU;
        $this->resolveNavigationOpenedParentKeys($opened, null, $items);

        return $opened;
    }
}
