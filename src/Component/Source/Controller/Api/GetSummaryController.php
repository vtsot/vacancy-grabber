<?php

declare(strict_types=1);

namespace App\Component\Source\Controller\Api;

use App\Component\Source\DTO\SourceSummaryDTO;
use App\Component\Source\Message\Query\GetSourceSummaryQuery;
use App\Contract\ApiDocTag;
use App\Service\MessageBusHandler;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetSummaryController extends AbstractController
{
    #[Route(
        path: '/api/sources/summary',
        name: 'api.source.summary',
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
    #[OA\Response(
        response: 200,
        description: 'Returns summary',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'items',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: SourceSummaryDTO::class)),
                ),
            ],
        )
    )]
    #[OA\Tag(name: ApiDocTag::SOURCE)]
    public function __invoke(
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        return $this->json(
            ['items' => $messageBusHandler->handleQuery(new GetSourceSummaryQuery())],
            context: ['groups' => ['source_summary']]
        );
    }
}
