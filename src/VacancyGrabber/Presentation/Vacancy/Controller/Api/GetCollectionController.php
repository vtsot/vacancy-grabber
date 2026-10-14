<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Vacancy\Controller\Api;

use App\VacancyGrabber\Application\Vacancy\DTO\VacancyCollectionDTO;
use App\VacancyGrabber\Application\Vacancy\Message\Query\GetVacancyCollectionQuery;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use App\VacancyGrabber\Infrastructure\Messenger\MessageBusHandler;
use App\VacancyGrabber\Infrastructure\Security\SecurityUser;
use App\VacancyGrabber\Presentation\Shared\ApiDocTag;
use App\VacancyGrabber\Presentation\Vacancy\ControllerRequest\Api\GetCollectionRequest;
use App\VacancyGrabber\Presentation\Vacancy\Factory\VacancyCriteriaFactory;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

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
        #[CurrentUser] SecurityUser $user,
        GetCollectionRequest $request,
        VacancyCriteriaFactory $factory,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        return $this->json(
            $messageBusHandler->handleQuery(
                new GetVacancyCollectionQuery(
                    $factory->fromCollectionRequest($user, $request)
                )
            ),
            context: ['groups' => ['collection_meta', 'vacancy_collection', 'source_summary', 'company_summary']]
        );
    }
}
