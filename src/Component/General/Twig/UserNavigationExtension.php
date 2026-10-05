<?php

declare(strict_types=1);

namespace App\Component\General\Twig;

use App\Component\General\DTO\HeaderNavigation\UserNavigationDTO;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserNavigationExtension extends AbstractExtension
{
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
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getUserNavigationItems', [$this, 'getUserNavigationItems']),
            new TwigFunction('getUserNavigationOpened', [$this, 'getUserNavigationOpened']),
        ];
    }

    /**
     * @return array<int, UserNavigationDTO>
     */
    public function getUserNavigationItems(): array
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        return $user
            ? $this->resolveUserNavigationWithSubItems(self::LOGGED_IN_USER_MENU)
            : $this->resolveUserNavigationWithSubItems(self::UNDEFINED_USER_MENU);
    }

    /**
     * @return array<int, UserNavigationDTO>
     */
    private function resolveUserNavigationWithSubItems(array $subItems = []): array
    {
        $items        = [];
        $request      = $this->requestStack->getMainRequest();
        $currentRoute = $request?->attributes->get('_route');

        foreach ($subItems as $item) {
            $active = ($currentRoute && ($item['route'] ?? null)) && $currentRoute === $item['route'];

            $subItems = ($item['items'] ?? [])
                ? $this->resolveUserNavigationWithSubItems($item['items'])
                : [];

            $items[] = new UserNavigationDTO(
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
    public function getUserNavigationOpened(): array
    {
        $opened = [];
        $user   = $this->tokenStorage->getToken()?->getUser();
        $items  = $user ? self::LOGGED_IN_USER_MENU : self::UNDEFINED_USER_MENU;
        $this->resolveUserNavigationOpenedParentKeys($opened, null, $items);

        return $opened;
    }

    private function resolveUserNavigationOpenedParentKeys(&$opened, ?string $parentKey, array $subItems = []): void
    {
        $request      = $this->requestStack->getMainRequest();
        $currentRoute = $request?->attributes->get('_route');

        foreach ($subItems as $item) {
            $active = ($currentRoute && ($item['route'] ?? null)) && $currentRoute === $item['route'];

            if ($active) {
                $opened[] = $parentKey;
            }

            if ($item['items'] ?? []) {
                $this->resolveUserNavigationOpenedParentKeys($opened, $item['key'], $item['items']);
            }
        }
    }
}
