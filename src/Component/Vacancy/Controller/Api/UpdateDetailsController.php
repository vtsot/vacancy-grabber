<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Controller\Api;

use App\Component\Vacancy\ControllerRequest\Api\UpdateDetailsRequest;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\Message\Command\UpdateVacancyDetailsCommand;
use App\Contract\ApiDocTag;
use App\Contract\Entity\UserInterface;
use App\Service\MessageBusHandler;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

final class UpdateDetailsController extends AbstractController
{
    #[Route(
        path: '/api/vacancies/{id}/details',
        name: 'api.vacancy.details.post',
        requirements: ['id' => Requirement::UUID],
        options: ['expose' => true],
        methods: ['POST']
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
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UpdateDetailsRequest::class)))]
    #[OA\Tag(name: ApiDocTag::VACANCY)]
    public function __invoke(
        #[CurrentUser] UserInterface $user,
        Uuid $id,
        UpdateDetailsRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(new UpdateVacancyDetailsCommand(
            userId: $user->getId(),
            vacancyId: $id,
            properties: $request->properties,
            fields: $request->fields,
            required: $request->required,
        ));

        return $this->json(data: $dto, context: ['groups' => ['vacancy_details']]);
    }
}
