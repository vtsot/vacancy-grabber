<?php

declare(strict_types=1);

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NoteController extends AbstractController
{
    #[Route(
        path: '/notes',
        name: 'web.note',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function index(): Response
    {
        return $this->render('web/note/index.html.twig', [
            'header_title' => 'Notes',
        ]);
    }
}
