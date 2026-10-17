<?php

declare(strict_types=1);

namespace App\Presentation\Dashboard\Controller\Api;

use App\Application\Dashboard\DTO\ResumeSentDashboardInfoCollectionDTO;
use App\Application\Dashboard\Message\Query\GetResumeSentDashboardQuery;
use App\Infrastructure\Messenger\MessageBusHandler;
use App\Infrastructure\Security\SecurityUser;
use App\Presentation\Shared\ApiDocTag;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GetResumeSentDashboardController extends AbstractController
{
    #[Route(
        path: '/api/dashboards/resume-sent',
        name: 'api.dashboard.resume-sent.collection',
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
        description: 'Returns collection',
        content: new OA\JsonContent(
            ref: new Model(
                type: ResumeSentDashboardInfoCollectionDTO::class,
                groups: ['dashboard']
            )),
    )]
    #[OA\Tag(name: ApiDocTag::DASHBOARD)]
    public function __invoke(
        #[CurrentUser] SecurityUser $user,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        return $this->json(
            $messageBusHandler->handleQuery(new GetResumeSentDashboardQuery(userId: $user->getId())),
            context: ['groups' => ['dashboard']]
        );
    }
}
