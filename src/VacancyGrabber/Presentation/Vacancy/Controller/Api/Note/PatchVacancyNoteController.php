<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Vacancy\Controller\Api\Note;

use App\VacancyGrabber\Application\Vacancy\DTO\VacancyDetailsDTO;
use App\VacancyGrabber\Application\Vacancy\Message\Command\PatchVacancyNoteCommand;
use App\VacancyGrabber\Infrastructure\Messenger\MessageBusHandler;
use App\VacancyGrabber\Infrastructure\Security\SecurityUser;
use App\VacancyGrabber\Presentation\Shared\ApiDocTag;
use App\VacancyGrabber\Presentation\Shared\Util;
use App\VacancyGrabber\Presentation\Vacancy\ControllerRequest\Api\PatchVacancyNoteRequest;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

final class PatchVacancyNoteController extends AbstractController
{
    #[Route(
        path: '/api/vacancies/{id}/notes/{noteId}',
        name: 'api.vacancy.note.patch',
        requirements: ['id' => Requirement::UUID, 'noteId' => Requirement::UUID],
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
            type: VacancyDetailsDTO::class,
            groups: ['vacancy_details'],
        ))
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: PatchVacancyNoteRequest::class)))]
    #[OA\Tag(name: ApiDocTag::VACANCY)]
    public function __invoke(
        #[CurrentUser] SecurityUser $user,
        Uuid $id,
        Uuid $noteId,
        PatchVacancyNoteRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(new PatchVacancyNoteCommand(
            userId: $user->getId(),
            vacancyId: $id,
            noteId: $noteId,
            properties: $request->properties,
            notedAt: Util::normalizeDate($request->notedAt),
            description: $request->description,
        ));

        return $this->json(data: $dto, context: ['groups' => ['vacancy_details']]);
    }
}
