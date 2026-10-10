<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Controller\Api;

use App\Component\General\Service\Util;
use App\Component\User\Contract\Entity\UserInterface;
use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\ControllerRequest\Api\CreateRequest;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\Message\Command\CreateVacancyCommand;
use App\Contract\ApiDocTag;
use App\Service\MessageBusHandler;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

final class CreateController extends AbstractController
{
    #[Route(
        path: '/api/vacancies',
        name: 'api.vacancy.post',
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
            type: VacancyDetailsDTO::class,
            groups: ['vacancy_details'],
        ))
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CreateRequest::class)))]
    #[OA\Tag(name: ApiDocTag::VACANCY)]
    public function __invoke(
        #[CurrentUser] UserInterface $user,
        CreateRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(new CreateVacancyCommand(
            userId: $user->getId(),
            title: $request->title,
            description: $request->description,
            externalId: $request->externalId,
            url: $request->url,
            salary: $request->salary,
            sourceId: $request->sourceId ? Uuid::fromString($request->sourceId) : null,
            companyId: $request->companyId ? Uuid::fromString($request->companyId) : null,
            status: $request->status ? VacancyStatus::tryFrom($request->status) : null,
            viewed: $request->viewed,
            resumeSentAt: Util::normalizeDate($request->resumeSentAt),
            deadLineAt: Util::normalizeDate($request->deadLineAt),
        ));

        return $this->json($dto, Response::HTTP_CREATED, ['groups' => ['vacancy_details']]);
    }
}
