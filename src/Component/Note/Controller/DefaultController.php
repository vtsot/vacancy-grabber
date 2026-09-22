<?php

declare(strict_types=1);

namespace App\Component\Note\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    #[Route(
        path: '/notes',
        name: 'web.note',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function __invoke(
        TranslatorInterface $translator,
    ): Response {
        return $this->render('@note_templates/web/index.html.twig', [
            'header_title' => $translator->trans('NOTE.HEADER_TITLE'),
        ]);
    }
}
