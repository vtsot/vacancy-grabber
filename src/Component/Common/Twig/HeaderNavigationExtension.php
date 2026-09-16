<?php

declare(strict_types=1);

namespace App\Component\Common\Twig;

use App\Component\Common\DTO\HeaderNavigation\HeaderNavigationDTO;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HeaderNavigationExtension extends AbstractExtension
{
    private const array NAVIGATION = [
        ['route' => 'web.source.list', 'title' => 'HEADER_NAVIGATION.SOURCE_LIST'],
        ['route' => 'web.grab-log.list', 'title' => 'HEADER_NAVIGATION.GRAB_LOG_LIST'],
        ['route' => 'web.company.list', 'title' => 'HEADER_NAVIGATION.COMPANY_LIST'],
        ['route' => 'web.vacancy.list', 'title' => 'HEADER_NAVIGATION.VACANCY_LIST'],
        ['route' => 'web.note.list', 'title' => 'HEADER_NAVIGATION.NOTE_LIST'],
    ];

    public function __construct(
        private readonly RouterInterface $router,
        private TranslatorInterface $translator,
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
                title: $this->translator->trans($item['title']),
                url: $this->router->generate($item['route'])
            );
        }

        return $items;
    }
}
