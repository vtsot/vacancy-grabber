<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\User\Controller\Api;

use App\Contract\ApiDocTag;
use OpenApi\Attributes as OA;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/auth')]
class AuthController extends AbstractController
{
    #[Route(
        path: '/login',
        name: 'api.auth.login',
        options: ['expose' => true],
        methods: ['POST']
    )]
    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    #[OA\RequestBody(
        request: 'body',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'username', type: 'string', format: 'email'),
                new OA\Property(property: 'password', type: 'string'),
            ],
            type: 'object',
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns login and refresh tokens',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'token', type: 'string'),
                new OA\Property(property: 'refresh_token', type: 'string'),
                new OA\Property(property: 'refresh_token_expiration', type: 'number'),
            ],
            type: 'object',
        )
    )]
    #[OA\Response(ref: '#/components/responses/invalid_json', response: '400')]
    #[OA\Response(ref: '#/components/responses/invalid_credentials', response: '401')]
    #[OA\Tag(name: ApiDocTag::AUTH)]
    public function login(): void
    {
        throw new RuntimeException('Should be handled by security component');
    }

    #[Route(
        path: '/refresh-token',
        name: 'api.auth.refresh_token',
        options: ['expose' => true],
        methods: ['POST']
    )]
    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    #[OA\RequestBody(
        request: 'body',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'refresh_token', type: 'string'),
            ],
            type: 'object',
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns login and refresh tokens',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'token', type: 'string'),
                new OA\Property(property: 'refresh_token', type: 'string'),
            ],
            type: 'object',
        )
    )]
    #[OA\Response(ref: '#/components/responses/invalid_json', response: '400')]
    #[OA\Response(ref: '#/components/responses/invalid_credentials', response: '401')]
    #[OA\Tag(name: ApiDocTag::AUTH)]
    public function refreshToken(): void
    {
        throw new RuntimeException('Should be handled by security component');
    }

    //    #[Route(
    //        path: '/forgot-password',
    //        name: 'api.auth.forgot_password',
    //        options: ['expose' => true],
    //        methods: ['POST']
    //    )]
    //    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    //    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: ForgotPasswordRequest::class)))]
    //    #[OA\Response(response: 204, description: 'No content on success')]
    //    #[OA\Response(ref: '#/components/responses/invalid_json', response: '400')]
    //    #[OA\Response(ref: '#/components/responses/not_found', response: '404')]
    //    #[OA\Tag(name: ApiDocTag::AUTH)]
    //    public function forgotPassword(
    //        ForgotPasswordRequest $request,
    //        MessageBusHandler $messageBusHandler,
    //        LocaleSwitcher $localeSwitcher,
    //    ): JsonResponse {
    //        $messageBusHandler->handleCommand(
    //            new SendForgotPasswordEmailCommand(
    //                locale: LocaleVO::fromString($localeSwitcher->getLocale()),
    //                email: $request->email
    //            )
    //        );
    //
    //        return $this->json(null, Response::HTTP_NO_CONTENT);
    //    }
    //
    //    #[Route(
    //        path: '/reset-password',
    //        name: 'api.auth.reset_password',
    //        options: ['expose' => true],
    //        methods: ['POST']
    //    )]
    //    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    //    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: ResetPasswordRequest::class)))]
    //    #[OA\Response(response: 204, description: 'No content on success')]
    //    #[OA\Response(ref: '#/components/responses/invalid_json', response: '400')]
    //    #[OA\Response(ref: '#/components/responses/not_found', response: '404')]
    //    #[OA\Response(ref: '#/components/responses/error', response: '422')]
    //    #[OA\Tag(name: ApiDocTag::AUTH)]
    //    public function resetPassword(
    //        ResetPasswordRequest $request,
    //        MessageBusHandler $messageBusHandler,
    //        LocaleSwitcher $localeSwitcher,
    //    ): JsonResponse {
    //        $messageBusHandler->handleCommand(
    //            new ResetPasswordCommand(
    //                locale: LocaleVO::fromString($localeSwitcher->getLocale()),
    //                email: $request->email,
    //                code: $request->code,
    //                password: $request->newPassword,
    //            )
    //        );
    //
    //        return $this->json(null, Response::HTTP_NO_CONTENT);
    //    }
}
