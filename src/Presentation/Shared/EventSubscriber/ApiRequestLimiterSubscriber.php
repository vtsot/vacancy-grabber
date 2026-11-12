<?php

declare(strict_types=1);

namespace App\Presentation\Shared\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

use function max;
use function str_starts_with;
use function time;

readonly class ApiRequestLimiterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RateLimiterFactoryInterface $apiRequestLimiter,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMainRequest() || !$this->isLimitedPath($request->getPathInfo())) {
            return;
        }

        $limit = $this->apiRequestLimiter->create($request->getClientIp() ?? 'unknown')->consume();

        if (!$limit->isAccepted()) {
            throw new TooManyRequestsHttpException(max(1, $limit->getRetryAfter()->getTimestamp() - time()), 'Too many requests.');
        }
    }

    /**
     * Only the public API (/api/...). Not ApiRequestDetector: it also matches any XHR / JSON-accepting request
     * (admin pages included) and "/api/" anywhere in the path. /api/admin and /api/doc are excluded as in security.yaml.
     */
    private function isLimitedPath(string $path): bool
    {
        return ($path === '/api' || str_starts_with($path, '/api/'))
            && !str_starts_with($path, '/api/admin')
            && !str_starts_with($path, '/api/doc');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // before the router (32) and the firewall (8), so rejected requests stay cheap
            KernelEvents::REQUEST => ['onKernelRequest', 64],
        ];
    }
}
