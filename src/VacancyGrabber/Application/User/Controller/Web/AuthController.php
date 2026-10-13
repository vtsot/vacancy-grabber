<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\User\Controller\Web;

use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Shared\Service\MessageBusHandler;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthController extends AbstractController
{
    #[Route(
        path: '/auth/login',
        name: 'web.auth.login',
        options: ['expose' => true],
        methods: ['POST', 'GET'],
    )]
    public function login(
        #[CurrentUser] ?UserInterface $user,
    ): Response {
        if ($user !== null) {
            return $this->redirectToRoute('web.homepage');
        }

        return $this->render('@user_templates/web/login.html.twig');
    }

    #[Route(
        path: '/auth/logout',
        name: 'web.auth.logout',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function logout(): Response
    {
        throw new RuntimeException('Should be handled by security component');
    }

    #[Route(
        path: '/auth/forgot-password',
        name: 'web.auth.forgot_password',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function forgotPassword(
        #[CurrentUser] ?UserInterface $user,
        MessageBusHandler $messageBusHandler,
    ): Response {
        if ($user !== null) {
            return $this->redirectToRoute('web.homepage');
        }

        return $this->render('@user_templates/web/forgot-password.html.twig');
    }
}
