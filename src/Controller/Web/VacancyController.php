<?php

declare(strict_types=1);

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VacancyController extends AbstractController
{
    #[Route(
        path: '/vacancies',
        name: 'web.vacancy',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function index(): Response
    {
        return $this->render('web/vacancy/index.html.twig', [
            'header_title' => 'Vacancies',
        ]);
    }
}
