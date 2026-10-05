<?php

declare(strict_types=1);

namespace App\Component\General\Twig;

use App\Component\General\DTO\HeaderNavigation\HeaderNavigationDTO;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HeaderNavigationExtension extends AbstractExtension
{
    private const array NAVIGATION = [
        [
            'key' => 'web.dashboard', 'route' => 'web.dashboard', 'title' => 'NAVIGATION.ITEM.DASHBOARD',
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
        [
            'key'   => 'vacancy-group',
            'route' => null,
            'title' => 'NAVIGATION.GROUP.VACANCY',
            'items' => [
                ['key' => 'web.source', 'route' => 'web.source', 'title' => 'NAVIGATION.ITEM.SOURCE'],
                ['key' => 'web.grab-log', 'route' => 'web.grab-log', 'title' => 'NAVIGATION.ITEM.GRAB_LOG'],
                ['key' => 'web.vacancy', 'route' => 'web.vacancy', 'title' => 'NAVIGATION.ITEM.VACANCY'],
            ],
        ],
    ];

    private const array LOGGED_IN_USER_MENU = [
        [
            'key'   => 'user-menu',
            'route' => null,
            'title' => 'NAVIGATION.USER.USER',
            'items' => [
                ['key' => 'web.auth.profile', 'route' => null/* 'web.auth.profile' */, 'title' => 'NAVIGATION.USER.PROFILE'],
                ['key' => 'web.auth.logout', 'route' => 'web.auth.logout', 'title' => 'NAVIGATION.USER.LOGOUT'],
            ],
        ],
    ];

    private const array UNDEFINED_USER_MENU = [
        [
            'key'   => 'web.auth.login',
            'route' => 'web.auth.login',
            'title' => 'NAVIGATION.USER.LOGIN',
        ],
    ];

    public function __construct(
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getHeaderNavigationItems', [$this, 'getHeaderNavigationItems']),
            new TwigFunction('getHeaderNavigationOpened', [$this, 'getHeaderNavigationOpened']),
        ];
    }

    /**
     * @return array<int, HeaderNavigationDTO>
     */
    public function getHeaderNavigationItems(): array
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        $navigation = $user
            ? $this->resolveHeaderNavigationWithSubItems(self::NAVIGATION)
            : [];

        $userMenu = $user
            ? $this->resolveHeaderNavigationWithSubItems(self::LOGGED_IN_USER_MENU)
            : $this->resolveHeaderNavigationWithSubItems(self::UNDEFINED_USER_MENU);

        return [...$navigation, ...$userMenu];
    }

    /**
     * @return array<int, HeaderNavigationDTO>
     */
    private function resolveHeaderNavigationWithSubItems(array $subItems = []): array
    {
        $items        = [];
        $request      = $this->requestStack->getMainRequest();
        $currentRoute = $request?->attributes->get('_route');

        foreach ($subItems as $item) {
            $active = ($currentRoute && ($item['route'] ?? null)) && $currentRoute === $item['route'];

            $subItems = ($item['items'] ?? [])
                ? $this->resolveHeaderNavigationWithSubItems($item['items'])
                : [];

            $items[] = new HeaderNavigationDTO(
                key: $item['key'],
                route: $item['route'] ?? null,
                url: ($item['route'] ?? null) ? $this->router->generate($item['route']) : null,
                title: $this->translator->trans($item['title']),
                active: $active,
                items: $subItems,
            );
        }

        return $items;
    }

    /**
     * @return array<int, string>
     */
    public function getHeaderNavigationOpened(): array
    {
        $opened = [];
        $this->resolveHeaderNavigationOpenedParentKeys($opened, null, self::NAVIGATION);

        return $opened;
    }

    private function resolveHeaderNavigationOpenedParentKeys(&$opened, ?string $parentKey, array $subItems = []): void
    {
        $request      = $this->requestStack->getMainRequest();
        $currentRoute = $request?->attributes->get('_route');

        foreach ($subItems as $item) {
            $active = ($currentRoute && ($item['route'] ?? null)) && $currentRoute === $item['route'];

            if ($active) {
                $opened[] = $parentKey;
            }

            if ($item['items'] ?? []) {
                $this->resolveHeaderNavigationOpenedParentKeys($opened, $item['key'], $item['items']);
            }
        }
    }
}
