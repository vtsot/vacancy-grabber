<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Controller\Api;

use App\Component\General\Service\Util;
use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\ControllerRequest\Api\GetCollectionRequest;
use App\Component\Vacancy\DTO\VacancyCollectionDTO;
use App\Component\Vacancy\Message\Query\GetVacancyCollectionQuery;
use App\Contract\ApiDocTag;
use App\Service\MessageBusHandler;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetCollectionController extends AbstractController
{
    #[Route(
        path: '/api/vacancies',
        name: 'api.vacancy.collection',
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
    // #[OA\Parameter(ref: '#/components/parameters/active')]
    #[OA\Parameter(ref: '#/components/parameters/start_day')]
    #[OA\Parameter(ref: '#/components/parameters/end_day')]
    #[OA\Parameter(name: 'company', in: 'query', required: false, schema: new OA\Schema(type: 'Uuid'))]
    #[OA\Parameter(name: 'source', in: 'query', required: false, schema: new OA\Schema(type: 'Uuid'))]
    #[OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(
        ref: new Model(
            type: VacancyStatus::class
        )
    ))]
    #[OA\Parameter(name: 'viewed', in: 'query', required: false, schema: new OA\Schema(type: 'Boolean'))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns collection',
        content: new OA\JsonContent(
            ref: new Model(
                type: VacancyCollectionDTO::class,
                groups: ['collection_meta', 'vacancy_collection', 'source_summary', 'company_summary']
            )
        ),
    )]
    #[OA\Tag(name: ApiDocTag::VACANCY)]
    public function __invoke(
        GetCollectionRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        return $this->json(
            $messageBusHandler->handleQuery(
                new GetVacancyCollectionQuery(
                    page: $request->page,
                    perPage: $request->perPage,
                    query: $request->query,
                    orderBy: $request->orderBy,
                    orderDirection: $request->orderDirection,
                    viewed: Util::normalizeBool($request->viewed),
                    startDay: Util::normalizeDate($request->startDay)?->startOfDay(),
                    endDay: Util::normalizeDate($request->endDay)?->endOfDay(),
                    source: $request->source,
                    company: $request->company,
                    status: $request->status ? VacancyStatus::tryFrom($request->status) : null,
                    isResumeSent: Util::normalizeBool($request->isResumeSent),
                    resumeSentAt: Util::normalizeDate($request->resumeSentAt),
                    resumeSentAtStartDay: Util::normalizeDate($request->resumeSentStartDay)?->startOfDay(),
                    resumeSentAtEndDay: Util::normalizeDate($request->resumeSentEndDay)?->endOfDay(),
                )
            ),
            context: ['groups' => ['collection_meta', 'vacancy_collection', 'source_summary', 'company_summary']]
        );
    }
}
