<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Shared\Security\Authenticator;

use App\VacancyGrabber\Application\Shared\DTO\ErrorDTO;
use App\VacancyGrabber\Infrastructure\Messenger\MessageBusHandler;
use App\VacancyGrabber\Infrastructure\Security\UserProvider\RegularUserProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidPayloadException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MainAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface, InteractiveAuthenticatorInterface
{
    use TargetPathTrait;

    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RegularUserProvider $userProvider,
        private readonly SerializerInterface $serializer,
        private readonly MessageBusHandler $messageBusHandler,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'web.auth.login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->request->get('token');

        try {
            if (!$payload = $this->jwtManager->parse($token)) {
                throw new InvalidTokenException('Invalid JWT Token');
            }
        } catch (JWTDecodeFailureException $e) {
            if ($e->getReason() === JWTDecodeFailureException::EXPIRED_TOKEN) {
                throw new ExpiredTokenException();
            }

            throw new InvalidTokenException('Invalid JWT Token', 0, $e);
        }

        $idClaim = $this->jwtManager->getUserIdClaim();

        if (!isset($payload[$idClaim])) {
            throw new InvalidPayloadException($idClaim);
        }

        $passport = new SelfValidatingPassport(
            new UserBadge(
                (string) $payload[$idClaim],
                function ($userIdentifier) {
                    return $this->userProvider->loadUserByIdentifier($userIdentifier);
                }
            )
        );

        $passport->setAttribute('payload', $payload);
        $passport->setAttribute('token', $token);

        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $request->attributes->get('target')
            ?? $request->request->get('target')
            ?? $request->query->get('target');

        if (!$targetPath && $request->hasSession()) {
            $targetPath = $this->getTargetPath($request->getSession(), 'main');
        }

        if (!$targetPath) {
            $targetPath = $this->urlGenerator->generate('web.dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return new JsonResponse(['redirect' => $targetPath]);
    }

    /**
     * @throws ExceptionInterface
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        }

        $json = $this->serializer->serialize(
            new ErrorDTO(Response::HTTP_UNAUTHORIZED, $exception->getMessage()),
            'json'
        );

        return new JsonResponse($json, Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, ?AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('web.auth.login'));
    }

    public function isInteractive(): bool
    {
        return true;
    }
}
