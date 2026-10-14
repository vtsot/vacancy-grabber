<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Twig\Navigation;

use App\VacancyGrabber\Application\DTO\Navigation\NavigationDTO;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;

abstract class AbstractNavigationExtension extends AbstractExtension
{
    protected readonly RouterInterface $router;
    protected readonly RequestStack $requestStack;
    protected readonly TranslatorInterface $translator;
    protected readonly TokenStorageInterface $tokenStorage;

    #[Required]
    public function initRequired(
        RouterInterface $router,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        TokenStorageInterface $tokenStorage,
    ): void {
        $this->router       = $router;
        $this->requestStack = $requestStack;
        $this->translator   = $translator;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return array<int, NavigationDTO>
     */
    protected function resolveNavigationWithSubItems(array $subItems = []): array
    {
        $items        = [];
        $request      = $this->requestStack->getMainRequest();
        $currentRoute = $request?->attributes->get('_route');

        foreach ($subItems as $item) {
            $active = ($currentRoute && ($item['route'] ?? null)) && $currentRoute === $item['route'];

            $subItems = ($item['items'] ?? [])
                ? $this->resolveNavigationWithSubItems($item['items'])
                : [];

            $url = ($item['route'] ?? null)
                ? $this->router->generate($item['route'], $item['params'] ?? [])
                : null;

            $items[] = new NavigationDTO(
                key: $item['key'],
                route: $item['route'] ?? null,
                url: $url,
                title: $this->translator->trans($item['title']),
                active: $active,
                prependIcon: $item['prependIcon'] ?? null,
                items: $subItems,
            );
        }

        return $items;
    }

    protected function resolveNavigationOpenedParentKeys(&$opened, ?string $parentKey, array $subItems = []): void
    {
        $request      = $this->requestStack->getMainRequest();
        $currentRoute = $request?->attributes->get('_route');

        foreach ($subItems as $item) {
            $active = ($currentRoute && ($item['route'] ?? null)) && $currentRoute === $item['route'];

            if ($active) {
                $opened[] = $parentKey;
            }

            if ($item['items'] ?? []) {
                $this->resolveNavigationOpenedParentKeys($opened, $item['key'], $item['items']);
            }
        }
    }
}
