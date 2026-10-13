<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Company\Controller\Api;

use App\Contract\ApiDocTag;
use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Company\ControllerRequest\Api\GetSummaryRequest;
use App\VacancyGrabber\Application\Company\DTO\CompanySummaryDTO;
use App\VacancyGrabber\Application\Company\DTOFactory\CompanyCriteriaDTOFactory;
use App\VacancyGrabber\Application\Company\Message\Query\GetCompanySummaryQuery;
use App\VacancyGrabber\Application\Shared\Service\MessageBusHandler;
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
        path: '/api/companies/summary',
        name: 'api.company.summary',
        options: ['expose' => true],
        methods: ['GET']
    )]
    // general
    #[OA\Parameter(ref: '#/components/parameters/accept_language')]
    #[OA\Parameter(name: 'with_note_only', in: 'query', required: false, schema: new OA\Schema(type: 'Boolean'))]
    #[OA\Parameter(name: 'with_vacancy_only', in: 'query', required: false, schema: new OA\Schema(type: 'Boolean'))]
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
                    items: new OA\Items(ref: new Model(type: CompanySummaryDTO::class)),
                ),
            ],
        )
    )]
    #[OA\Tag(name: ApiDocTag::COMPANY)]
    public function __invoke(
        #[CurrentUser] UserInterface $user,
        GetSummaryRequest $request,
        CompanyCriteriaDTOFactory $factory,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        return $this->json([
            'items' => $messageBusHandler->handleQuery(
                new GetCompanySummaryQuery(
                    $factory->fromSummaryRequest($user, $request)
                )
            )],
            context: ['groups' => ['company_summary']]
        );
    }
}
