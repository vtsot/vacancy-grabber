<?php

declare(strict_types=1);

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CompanyController extends AbstractController
{
    #[Route(
        path: '/companies',
        name: 'web.company',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function index(): Response
    {
        return $this->render('web/company/index.html.twig', [
            'header_title' => 'Companies',
        ]);
    }
}
