<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\Controller\Api;

use App\Contract\ApiDocTag;
use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Shared\Service\MessageBusHandler;
use App\VacancyGrabber\Application\Source\ControllerRequest\Api\CreateRequest;
use App\VacancyGrabber\Application\Source\DTO\SourceDetailsDTO;
use App\VacancyGrabber\Application\Source\Message\Command\CreateSourceCommand;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class CreateController extends AbstractController
{
    #[Route(
        path: '/api/sources',
        name: 'api.source.post',
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
            type: SourceDetailsDTO::class,
            groups: ['source_details'],
        ))
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CreateRequest::class)))]
    #[OA\Tag(name: ApiDocTag::SOURCE)]
    public function __invoke(
        #[CurrentUser] UserInterface $user,
        CreateRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(
            new CreateSourceCommand(
                userId: $user->getId(),
                name: $request->name,
                url: $request->url,
                active: $request->active,
            ),
        );

        return $this->json($dto, Response::HTTP_CREATED, ['groups' => ['source_details']]);
    }
}
