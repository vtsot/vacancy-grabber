<?php

declare(strict_types=1);

namespace App\Presentation\User\EventSubscriber;

use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\Security\SecurityUser;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Twig\Environment as Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use function sprintf;
use function str_starts_with;

readonly class AdminTwoStepAuthListener implements EventSubscriberInterface
{
    private const string ISSUER = 'VacancyGrabber';

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function __construct(
        private Twig $twig,
        private UserRepository $userRepository,
        private GoogleAuthenticator $authenticator,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!str_starts_with($event->getRequest()->getPathInfo(), '/admin')) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token instanceof TokenInterface) {
            return;
        }

        $user = $token?->getUser();

        if (!$user instanceof SecurityUser) {
            return;
        }

        // GoogleAuthenticator is disabled
        if (!$user->getUser()->isGoogleAuthenticatorEnabled()) {
            return;
        }

        // GoogleAuthenticator is enabled
        // process it
        $request    = $event->getRequest();
        $sessionKey = sprintf('_user_two_step_auth_%s_%s', 'admin', $token->getUserIdentifier());

        // google auth token is not set yet
        if ($user->getUser()->getGoogleAuthenticatorToken() === null) {
            $event->setResponse($this->handleAuthResponse($request, $user, $sessionKey));

            return;
        }

        // google auth token was set
        // confirm the code
        if (!$request->getSession()->has($sessionKey)
            || $request->getSession()->get($sessionKey) !== true
        ) {
            $event->setResponse($this->handleConfirmResponse($request, $user, $sessionKey));
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function handleAuthResponse(
        Request $request,
        SecurityUser $user,
        string $sessionKey,
    ): Response {
        $session   = $request->getSession();
        $secretKey = $sessionKey . '_secret';

        if (!$session->has($secretKey)) {
            $session->set($secretKey, $this->authenticator->generateSecret());
        }

        $error  = false;
        $secret = $session->get($secretKey);

        // handle post request
        if ($request->isMethod(Request::METHOD_POST)) {
            if ($this->authenticator->checkCode($secret, $request->request->get('_code'))) {
                // update session
                $session->set($sessionKey, true);
                $session->remove($secretKey);

                // update user's $secret
                $user->getUser()->pairGoogleAuthenticator($secret);
                $this->userRepository->save($user->getUser());

                return new RedirectResponse($request->getUri());
            }

            $error = true;
        }

        return new Response(
            $this->twig->render(
                '@user_templates/admin/security/google-authentication.html.twig',
                [
                    'validate'  => false,
                    'error'     => $error,
                    'qrCodeImg' => GoogleQrUrl::generate($user->getUserIdentifier(), $secret, self::ISSUER),
                ]
            )
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function handleConfirmResponse(
        Request $request,
        SecurityUser $user,
        string $sessionKey,
    ): Response {
        $session = $request->getSession();
        $error   = false;

        // handle post request
        if ($request->isMethod(Request::METHOD_POST)) {
            if ($this->authenticator->checkCode(
                $user->getUser()->getGoogleAuthenticatorToken(),
                $request->request->get('_code')
            )) {
                $session->set($sessionKey, true);

                return new RedirectResponse($request->getUri());
            }

            $error = true;
        }

        return new Response(
            $this->twig->render(
                '@user_templates/admin/security/google-authentication.html.twig',
                [
                    'validate' => true,
                    'error'    => $error,
                ]
            )
        );
    }
}
