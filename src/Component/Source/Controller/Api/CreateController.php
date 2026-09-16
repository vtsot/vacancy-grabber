<?php

declare(strict_types=1);

namespace App\Component\Source\Controller\Api;

use App\Component\Source\ControllerRequest\Api\CreateRequest;
use App\Component\Source\DTO\SourceDetailsDTO;
use App\Component\Source\Message\Command\CreateSourceCommand;
use App\Contract\ApiDocTag;
use App\Controller\AbstractController;
use App\Service\MessageBusHandler;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreateController extends AbstractController
{
    #[Route(
        path: '/api/sources',
        name: 'api.source.post',
        methods: ['POST']
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
            type: SourceDetailsDTO::class,
            groups: ['source_details'],
        ))
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CreateRequest::class)))]
    #[OA\Tag(name: ApiDocTag::SOURCE)]
    public function __invoke(
        CreateRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(
            new CreateSourceCommand(
                name: $request->name,
                url: $request->url,
                active: $request->active,
            ),
        );

        return $this->json($dto, Response::HTTP_CREATED, ['groups' => ['source_details']]);
    }
}
