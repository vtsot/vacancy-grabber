<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Vacancy\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    #[Route(
        path: '/vacancies',
        name: 'web.vacancy',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function __invoke(
        TranslatorInterface $translator,
    ): Response {
        return $this->render('@vacancy_templates/web/index.html.twig', [
            'header_title' => $translator->trans('VACANCY.HEADER_TITLE'),
        ]);
    }
}
