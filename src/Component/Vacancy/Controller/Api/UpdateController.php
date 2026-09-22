<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Controller\Api;

use App\Component\Vacancy\ControllerRequest\Api\UpdateRequest;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\Message\Command\UpdateVacancyCommand;
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

final class UpdateController extends AbstractController
{
    #[Route(
        path: '/api/vacancies/{id}',
        name: 'api.vacancy.put',
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
            type: VacancyDetailsDTO::class,
            groups: ['vacancy_details'],
        ))
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UpdateRequest::class)))]
    #[OA\Tag(name: ApiDocTag::VACANCY)]
    public function __invoke(
        Uuid $id,
        UpdateRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(
            new UpdateVacancyCommand(
                id: $id,
                name: $request->name,
                url: $request->url,
                active: $request->active,
            ),
        );

        return $this->json(data: $dto, context: ['groups' => ['vacancy_details']]);
    }
}
