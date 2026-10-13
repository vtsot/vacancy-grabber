<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\Controller\Api;

use App\Contract\ApiDocTag;
use App\Contract\Entity\UserInterface;
use App\Service\MessageBusHandler;
use App\VacancyGrabber\Application\Source\ControllerRequest\Api\UpdateRequest;
use App\VacancyGrabber\Application\Source\DTO\SourceDetailsDTO;
use App\VacancyGrabber\Application\Source\Message\Command\UpdateSourceCommand;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

final class UpdateController extends AbstractController
{
    #[Route(
        path: '/api/sources/{id}',
        name: 'api.source.put',
        requirements: ['id' => Requirement::UUID],
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
            type: SourceDetailsDTO::class,
            groups: ['source_details'],
        ))
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UpdateRequest::class)))]
    #[OA\Tag(name: ApiDocTag::SOURCE)]
    public function __invoke(
        #[CurrentUser] UserInterface $user,
        Uuid $id,
        UpdateRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(
            new UpdateSourceCommand(
                userId: $user->getId(),
                sourceId: $id,
                name: $request->name,
                url: $request->url,
                active: $request->active,
            ),
        );

        return $this->json(data: $dto, context: ['groups' => ['source_details']]);
    }
}
