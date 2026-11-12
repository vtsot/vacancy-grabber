<?php

declare(strict_types=1);

namespace App\Presentation\Shared\EventSubscriber;

use App\Presentation\Shared\Service\Request\ApiRequestDetector;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\LocaleSwitcher;

use function is_string;

readonly class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(param: 'default_locale')]
        private string $defaultLocale,
        private LocaleSwitcher $localeSwitcher,
        private ApiRequestDetector $apiRequestDetector,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $locale  = $request->attributes->get('_locale');

        // locale chosen with the admin global locale switcher, see sonata_translation.locale_switcher
        // checked before Accept-Language: admin XHR requests count as API requests but must keep the admin's locale
        if (!is_string($locale) && $request->hasPreviousSession()) {
            $locale = $request->getSession()->get('_locale');
        }

        if (!is_string($locale) && $this->apiRequestDetector->isApiRequest($request)) {
            $locale = $request->getPreferredLanguage();
        }

        if (!is_string($locale) || $locale === '') {
            $locale = $this->defaultLocale;
        }

        $this->localeSwitcher->setLocale($locale);
        $request->setLocale($locale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
