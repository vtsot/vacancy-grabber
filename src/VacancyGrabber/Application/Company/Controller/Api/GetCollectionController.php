<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Company\Controller\Api;

use App\Contract\ApiDocTag;
use App\Contract\Entity\UserInterface;
use App\Service\MessageBusHandler;
use App\VacancyGrabber\Application\Company\ControllerRequest\Api\GetCollectionRequest;
use App\VacancyGrabber\Application\Company\DTO\CompanyCollectionDTO;
use App\VacancyGrabber\Application\Company\DTOFactory\CompanyCriteriaDTOFactory;
use App\VacancyGrabber\Application\Company\Message\Query\GetCompanyCollectionQuery;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GetCollectionController extends AbstractController
{
    #[Route(
        path: '/api/companies',
        name: 'api.company.collection',
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
    #[OA\Parameter(ref: '#/components/parameters/page')]
    #[OA\Parameter(ref: '#/components/parameters/per_page')]
    #[OA\Parameter(ref: '#/components/parameters/query')]
    // #[OA\Parameter(ref: '#/components/parameters/sort')]
    #[OA\Parameter(
        name: 'sort',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            type: 'string',
            default: 'created_at asc'
        ),
        examples: [
            new OA\Examples(example: 'created_at asc', summary: 'Sort by created_at asc', value: 'created_at asc'),
            new OA\Examples(example: 'created_at desc', summary: 'Sort by created_at desc', value: 'created_at desc'),
            new OA\Examples(example: 'name asc', summary: 'Sort by name asc', value: 'name asc'),
            new OA\Examples(example: 'name desc', summary: 'Sort by name desc', value: 'name desc'),
            new OA\Examples(example: 'url asc', summary: 'Sort by url asc', value: 'url asc'),
            new OA\Examples(example: 'url desc', summary: 'Sort by url desc', value: 'url desc'),
        ]
    )]
    #[OA\Parameter(ref: '#/components/parameters/active')]
    #[OA\Parameter(ref: '#/components/parameters/start_day')]
    #[OA\Parameter(ref: '#/components/parameters/end_day')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns collection',
        content: new OA\JsonContent(
            ref: new Model(
                type: CompanyCollectionDTO::class,
                groups: ['collection_meta', 'company_collection']
            )
        ),
    )]
    #[OA\Tag(name: ApiDocTag::COMPANY)]
    public function __invoke(
        #[CurrentUser] UserInterface $user,
        GetCollectionRequest $request,
        CompanyCriteriaDTOFactory $factory,
        MessageBusHandler $messageBusHandler,
    ): JsonResponse {
        return $this->json(
            $messageBusHandler->handleQuery(
                new GetCompanyCollectionQuery(
                    $factory->fromCollectionRequest($user, $request)
                )),
            context: ['groups' => ['collection_meta', 'company_collection']]
        );
    }
}
