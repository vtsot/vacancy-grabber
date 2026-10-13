<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\Controller\Api;

use App\Contract\ApiDocTag;
use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Shared\Service\MessageBusHandler;
use App\VacancyGrabber\Application\Source\DTO\SourceSummaryDTO;
use App\VacancyGrabber\Application\Source\Message\Query\GetSourceSummaryQuery;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

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
        #[CurrentUser] UserInterface $user,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        return $this->json([
            'items' => $messageBusHandler->handleQuery(
                new GetSourceSummaryQuery(
                    userId: $user->getId()
                )),
        ],
            context: ['groups' => ['source_summary']]
        );
    }
}
