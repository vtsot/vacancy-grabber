<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Controller\Api\Note;

use App\Component\General\Service\Util;
use App\Component\User\Contract\Entity\UserInterface;
use App\Component\Vacancy\ControllerRequest\Api\CreateVacancyNoteRequest;
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
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

final class CreateVacancyNoteController extends AbstractController
{
    #[Route(
        path: '/api/vacancies/{id}/notes',
        name: 'api.vacancy.note.post',
        requirements: ['id' => Requirement::UUID],
        options: ['expose' => true],
        methods: ['POST'],
    )]
    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    #[OA\Response(ref: '#/components/responses/error', response: Response::HTTP_UNPROCESSABLE_ENTITY)]
    #[OA\Response(ref: '#/components/responses/invalid_json', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/invalid_credentials', response: Response::HTTP_UNAUTHORIZED)]
    #[OA\Response(ref: '#/components/responses/forbidden', response: Response::HTTP_FORBIDDEN)]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Return details',
        content: new OA\JsonContent(ref: new Model(
            type: VacancyNoteDTO::class,
            groups: ['vacancy_note_details'],
        ))
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CreateVacancyNoteRequest::class)))]
    #[OA\Tag(name: ApiDocTag::VACANCY)]
    public function __invoke(
        #[CurrentUser] UserInterface $user,
        Uuid $id,
        CreateVacancyNoteRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(new CreateVacancyNoteCommand(
            userId: $user->getId(),
            vacancyId: $id,
            notedAt: Util::normalizeDate($request->notedAt),
            description: $request->description,
        ));

        return $this->json($dto, Response::HTTP_CREATED, ['groups' => ['vacancy_note_details']]);
    }
}
