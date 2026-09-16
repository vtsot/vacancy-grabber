<?php

declare(strict_types=1);

namespace App\Component\Source\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route(
        path: '/sources',
        name: 'web.source',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function index(): Response
    {
        return $this->render('@source_templates/index.html.twig', [
            'header_title' => 'Sources',
        ]);
    }
}
