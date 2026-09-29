<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Controller\Api\Note;

use App\Component\General\Service\Util;
use App\Component\Vacancy\ControllerRequest\Api\UpdateVacancyNoteRequest;
use App\Component\Vacancy\DTO\VacancyNoteDTO;
use App\Component\Vacancy\Message\Command\CreateVacancyNoteCommand;
use App\Contract\ApiDocTag;
use App\Service\MessageBusHandler;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\Uuid;

final class UpdateVacancyNoteController extends AbstractController
{
    #[Route(
        path: '/api/vacancies/{id}/notes/{noteId}',
        name: 'api.vacancy.note.put',
        requirements: ['id' => Requirement::UUID, 'noteId' => Requirement::UUID],
        options: ['expose' => true],
        methods: ['PUT']
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
            type: VacancyNoteDTO::class,
            groups: ['vacancy_details'],
        ))
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UpdateVacancyNoteRequest::class)))]
    #[OA\Tag(name: ApiDocTag::VACANCY)]
    public function __invoke(
        Uuid $id,
        Uuid $noteId,
        UpdateVacancyNoteRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(new CreateVacancyNoteCommand(
            vacancyId: $id,
            noteId: $noteId,
            notedAt: Util::normalizeDate($request->notedAt),
            description: $request->description,
        ));

        return $this->json(data: $dto, context: ['groups' => ['vacancy_note_details']]);
    }
}
