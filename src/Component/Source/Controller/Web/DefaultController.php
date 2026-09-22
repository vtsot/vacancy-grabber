<?php

declare(strict_types=1);

namespace App\Component\Source\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    #[Route(
        path: '/sources',
        name: 'web.source',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function __invoke(
        TranslatorInterface $translator,
    ): Response {
        return $this->render('@source_templates/web/index.html.twig', [
            'header_title' => $translator->trans('SOURCE.HEADER_TITLE'),
        ]);
    }
}
