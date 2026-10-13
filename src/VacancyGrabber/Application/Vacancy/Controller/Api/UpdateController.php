<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\Controller\Api;

use App\Contract\ApiDocTag;
use App\Contract\Entity\UserInterface;
use App\Service\MessageBusHandler;
use App\VacancyGrabber\Application\Shared\Service\Util;
use App\VacancyGrabber\Application\Vacancy\ControllerRequest\Api\UpdateRequest;
use App\VacancyGrabber\Application\Vacancy\DTO\VacancyDetailsDTO;
use App\VacancyGrabber\Application\Vacancy\Message\Command\UpdateVacancyCommand;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
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
        #[CurrentUser] UserInterface $user,
        Uuid $id,
        UpdateRequest $request,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        $dto = $messageBusHandler->handleCommand(new UpdateVacancyCommand(
            userId: $user->getId(),
            vacancyId: $id,
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

        return $this->json(data: $dto, context: ['groups' => ['vacancy_details']]);
    }
}
