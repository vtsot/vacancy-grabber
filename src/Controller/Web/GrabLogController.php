<?php

declare(strict_types=1);

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GrabLogController extends AbstractController
{
    #[Route(
        path: '/grab-logs',
        name: 'web.grab-log',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function index(): Response
    {
        return $this->render('web/grab-log/index.html.twig', [
            'header_title' => 'Grab Logs',
        ]);
    }
}
