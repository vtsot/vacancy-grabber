<?php

declare(strict_types=1);

namespace App\Component\Source\Controller\Api;

use App\Component\Source\DTO\SourceDetailsDTO;
use App\Component\Source\Message\Query\GetSourceDetailsQuery;
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

final class GetDetailsController extends AbstractController
{
    #[Route(
        path: '/api/sources/{id}',
        name: 'api.source.details',
        requirements: ['id' => Requirement::UUID],
        options: ['expose' => true],
        methods: ['GET']
    )]
    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    #[OA\Response(ref: '#/components/responses/error', response: Response::HTTP_UNPROCESSABLE_ENTITY)]
    #[OA\Response(ref: '#/components/responses/invalid_json', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/invalid_credentials', response: Response::HTTP_UNAUTHORIZED)]
    #[OA\Response(ref: '#/components/responses/forbidden', response: Response::HTTP_FORBIDDEN)]
    #[OA\Response(ref: '#/components/responses/not_found', response: Response::HTTP_NOT_FOUND)]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns details',
        content: new OA\JsonContent(
            ref: new Model(
                type: SourceDetailsDTO::class,
                groups: ['source_details']
            )
        ),
    )]
    #[OA\Tag(name: ApiDocTag::SOURCE)]
    public function __invoke(
        Uuid $id,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleQuery(new GetSourceDetailsQuery($id));

        return $this->json(data: $dto, context: ['groups' => ['source_details']]);
    }
}
