<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Vacancy\Controller\Api\Note;

use App\Contract\ApiDocTag;
use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Component\Vacancy\ControllerRequest\Api\GetNoteCollectionRequest;
use App\VacancyGrabber\Application\Component\Vacancy\DTO\VacancyNoteCollectionDTO;
use App\VacancyGrabber\Application\Component\Vacancy\Message\Query\GetVacancyNoteCollectionQuery;
use App\VacancyGrabber\Application\Service\MessageBusHandler;
use App\VacancyGrabber\Application\Shared\Util;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

class GetVacancyNoteCollectionController extends AbstractController
{
    #[Route(
        path: '/api/vacancies/{id}/notes',
        name: 'api.vacancy.note.collection',
        requirements: ['id' => Requirement::UUID],
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
    #[OA\Parameter(ref: '#/components/parameters/page')]
    #[OA\Parameter(ref: '#/components/parameters/per_page')]
    #[OA\Parameter(ref: '#/components/parameters/query')]
    // #[OA\Parameter(ref: '#/components/parameters/sort')]
    #[OA\Parameter(
        name: 'sort',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            type: 'string',
            default: 'created_at asc'
        ),
        examples: [
            new OA\Examples(example: 'created_at asc', summary: 'Sort by created_at asc', value: 'created_at asc'),
            new OA\Examples(example: 'created_at desc', summary: 'Sort by created_at desc', value: 'created_at desc'),
        ]
    )]
    #[OA\Parameter(ref: '#/components/parameters/start_day')]
    #[OA\Parameter(ref: '#/components/parameters/end_day')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns collection',
        content: new OA\JsonContent(
            ref: new Model(
                type: VacancyNoteCollectionDTO::class,
                groups: ['collection_meta', 'vacancy_note_collection', 'vacancy_note_info']
            )
        ),
    )]
    #[OA\Tag(name: ApiDocTag::VACANCY)]
    public function __invoke(
        #[CurrentUser] UserInterface $user,
        Uuid $id,
        GetNoteCollectionRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        return $this->json(
            $messageBusHandler->handleQuery(
                new GetVacancyNoteCollectionQuery(
                    userId: $user->getId(),
                    vacancyId: $id,
                    page: $request->page,
                    perPage: $request->perPage,
                    query: $request->query,
                    orderBy: $request->orderBy,
                    orderDirection: $request->orderDirection,
                    startDay: Util::normalizeDate($request->startDay)?->startOfDay(),
                    endDay: Util::normalizeDate($request->endDay)?->endOfDay(),
                    notedAtStartDay: Util::normalizeDate($request->notedAtStartDay)?->startOfDay(),
                    notedAtEndDay: Util::normalizeDate($request->notedAtEndDay)?->endOfDay(),
                )
            ),
            context: ['groups' => ['collection_meta', 'vacancy_note_collection', 'vacancy_note_info']]
        );
    }
}
