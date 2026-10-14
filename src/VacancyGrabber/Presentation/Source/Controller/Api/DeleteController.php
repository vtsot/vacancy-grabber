<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Source\Controller\Api;

use App\VacancyGrabber\Application\Source\Message\Command\DeleteSourceCommand;
use App\VacancyGrabber\Infrastructure\Messenger\MessageBusHandler;
use App\VacancyGrabber\Infrastructure\Security\SecurityUser;
use App\VacancyGrabber\Presentation\Shared\ApiDocTag;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

final class DeleteController extends AbstractController
{
    #[Route(
        path: '/api/sources/{id}',
        name: 'api.source.delete',
        requirements: ['id' => Requirement::UUID],
        options: ['expose' => true],
        methods: ['DELETE']
    )]
    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    #[OA\Response(ref: '#/components/responses/error', response: Response::HTTP_UNPROCESSABLE_ENTITY)]
    #[OA\Response(ref: '#/components/responses/invalid_json', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/invalid_credentials', response: Response::HTTP_UNAUTHORIZED)]
    #[OA\Response(ref: '#/components/responses/forbidden', response: Response::HTTP_FORBIDDEN)]
    #[OA\Response(ref: '#/components/responses/not_found', response: Response::HTTP_NOT_FOUND)]
    #[OA\Response(ref: '#/components/responses/no_content', response: Response::HTTP_NO_CONTENT)]
    #[OA\Tag(name: ApiDocTag::SOURCE)]
    public function __invoke(
        #[CurrentUser] SecurityUser $user,
        Uuid $id,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $messageBusHandler->handleCommand(new DeleteSourceCommand(userId: $user->getId(), sourceId: $id));

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
