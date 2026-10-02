<?php

declare(strict_types=1);

namespace App\Component\Company\Controller\Web;

use App\Component\User\Contract\Entity\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    #[Route(
        path: '/companies',
        name: 'web.company',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function __invoke(
        #[CurrentUser] UserInterface $user,
        TranslatorInterface $translator,
    ): Response {
        return $this->render('@company_templates/web/index.html.twig', [
            'header_title' => $translator->trans('COMPANY.HEADER_TITLE'),
        ]);
    }
}
