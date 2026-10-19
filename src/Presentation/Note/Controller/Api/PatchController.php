<?php

declare(strict_types=1);

namespace App\Presentation\Note\Controller\Api;

use App\Application\Note\DTO\NoteDetailsDTO;
use App\Application\Note\Message\Command\PatchNoteCommand;
use App\Infrastructure\Messenger\MessageBusHandler;
use App\Infrastructure\Security\SecurityUser;
use App\Presentation\Note\ControllerRequest\Api\PatchRequest;
use App\Presentation\Shared\ApiDocTag;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

final class PatchController extends AbstractController
{
    #[Route(
        path: '/api/notes/{id}',
        name: 'api.note.patch',
        requirements: ['id' => Requirement::UUID],
        options: ['expose' => true],
        methods: ['PATCH']
    )]
    // general
    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    #[OA\Response(ref: '#/components/responses/error', response: Response::HTTP_UNPROCESSABLE_ENTITY)]
    #[OA\Response(ref: '#/components/responses/invalid_json', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/invalid_credentials', response: Response::HTTP_UNAUTHORIZED)]
    #[OA\Response(ref: '#/components/responses/forbidden', response: Response::HTTP_FORBIDDEN)]
    #[OA\Response(ref: '#/components/responses/not_found', response: Response::HTTP_NOT_FOUND)]
    // custom
    #[OA\Response(
        response: 200,
        description: 'Return details',
        content: new OA\JsonContent(ref: new Model(
            type: NoteDetailsDTO::class,
            groups: ['note_details'],
        ))
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: PatchRequest::class)))]
    #[OA\Tag(name: ApiDocTag::NOTE)]
    public function __invoke(
        #[CurrentUser] SecurityUser $user,
        Uuid $id,
        PatchRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(new PatchNoteCommand(
            userId: $user->getId(),
            noteId: $id,
            properties: $request->properties,
            notedAt: $request->notedAt,
            description: $request->description,
        ));

        return $this->json(data: $dto, context: ['groups' => ['note_details']]);
    }
}
