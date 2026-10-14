<?php

declare(strict_types=1);

namespace App\Presentation\GrabLog\Controller\Api;

use App\Application\GrabLog\DTO\GrabLogCollectionDTO;
use App\Application\GrabLog\Message\Query\GetGrabLogCollectionQuery;
use App\Infrastructure\Messenger\MessageBusHandler;
use App\Infrastructure\Security\SecurityUser;
use App\Presentation\GrabLog\ControllerRequest\Api\GetCollectionRequest;
use App\Presentation\GrabLog\Factory\GrabLogCriteriaFactory;
use App\Presentation\Shared\ApiDocTag;
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
        path: '/api/grab-logs',
        name: 'api.grab-log.collection',
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
                type: GrabLogCollectionDTO::class,
                groups: ['collection_meta', 'grab_log_collection']
            )
        ),
    )]
    #[OA\Tag(name: ApiDocTag::GRAB_LOG)]
    public function __invoke(
        #[CurrentUser] SecurityUser $user,
        GetCollectionRequest $request,
        GrabLogCriteriaFactory $factory,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        return $this->json(
            $messageBusHandler->handleQuery(
                new GetGrabLogCollectionQuery(
                    $factory->fromCollectionRequest($user, $request)
                )
            ),
            context: ['groups' => ['collection_meta', 'grab_log_collection']]
        );
    }
}
