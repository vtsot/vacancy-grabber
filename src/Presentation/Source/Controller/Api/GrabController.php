<?php

declare(strict_types=1);

namespace App\Presentation\Source\Controller\Api;

use App\Application\Source\Message\Command\GrabSourceCommand;
use App\Infrastructure\Messenger\MessageBusHandler;
use App\Infrastructure\Security\SecurityUser;
use App\Presentation\Shared\ApiDocTag;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

final class GrabController extends AbstractController
{
    #[Route(
        path: '/api/sources/{id}/grab',
        name: 'api.source.grab',
        requirements: ['id' => Requirement::UUID],
        options: ['expose' => true],
        methods: ['POST']
    )]
    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    #[OA\Response(ref: '#/components/responses/error', response: Response::HTTP_UNPROCESSABLE_ENTITY)]
    #[OA\Response(ref: '#/components/responses/invalid_json', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/invalid_credentials', response: Response::HTTP_UNAUTHORIZED)]
    #[OA\Response(ref: '#/components/responses/forbidden', response: Response::HTTP_FORBIDDEN)]
    #[OA\Response(ref: '#/components/responses/not_found', response: Response::HTTP_NOT_FOUND)]
    // custom
    #[OA\Response(
        response: 200,
        description: 'Returns stat details',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'counter_vacancy_created', type: 'number', default: 0),
            ],
        )
    )]
    #[OA\Tag(name: ApiDocTag::SOURCE)]
    public function __invoke(
        #[CurrentUser] SecurityUser $user,
        Uuid $id,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $counterVacancyCreated = $messageBusHandler->handleCommand(new GrabSourceCommand(userId: $user->getId(), sourceId: $id));

        return $this->json(['counter_vacancy_created' => $counterVacancyCreated]);
    }
}
