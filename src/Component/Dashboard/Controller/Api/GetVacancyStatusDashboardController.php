<?php

declare(strict_types=1);

namespace App\Component\Dashboard\Controller\Api;

use App\Component\Dashboard\DTO\VacancyStatusDashboardDTO;
use App\Component\Dashboard\Message\Query\GetVacancyStatusDashboardQuery;
use App\Component\User\Contract\Entity\UserInterface;
use App\Contract\ApiDocTag;
use App\Service\MessageBusHandler;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GetVacancyStatusDashboardController extends AbstractController
{
    #[Route(
        path: '/api/dashboards/vacancy-status',
        name: 'api.dashboard.vacancy-status',
        options: ['expose' => true],
        methods: ['GET']
    )]
    // general
    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    #[OA\Response(ref: '#/components/responses/error', response: Response::HTTP_UNPROCESSABLE_ENTITY)]
    #[OA\Response(ref: '#/components/responses/invalid_json', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/invalid_credentials', response: Response::HTTP_UNAUTHORIZED)]
    #[OA\Response(ref: '#/components/responses/forbidden', response: Response::HTTP_FORBIDDEN)]
    // custom
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns details',
        content: new OA\JsonContent(
            ref: new Model(
                type: VacancyStatusDashboardDTO::class,
                groups: ['dashboard']
            )),
    )]
    #[OA\Tag(name: ApiDocTag::DASHBOARD)]
    public function __invoke(
        #[CurrentUser] UserInterface $user,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        return $this->json(
            $messageBusHandler->handleQuery(new GetVacancyStatusDashboardQuery(userId: $user->getId())),
            context: ['groups' => ['dashboard']]
        );
    }
}
