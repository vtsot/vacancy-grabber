<?php

declare(strict_types=1);

namespace App\Presentation\Source\Controller\Admin;

use App\Application\Source\DTO\GrabSourceResultDTO;
use App\Application\Source\Message\Command\GrabSourceCommand;
use App\Domain\Source\Entity\Source;
use App\Infrastructure\Messenger\MessageBusHandler;
use App\Presentation\Shared\Admin\AbstractAdminCrudController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

use function assert;

class SourceAdminCrudController extends AbstractAdminCrudController
{
    public function __construct(
        private readonly MessageBusHandler $messageBusHandler,
    ) {
    }

    public function grabAction(Request $request): Response
    {
        /** @var Source $object */
        $object = $this->assertObjectExists($request, true);
        assert($object !== null);

        $this->checkParentChildAssociation($request, $object);

        $this->admin->checkAccess('grab', $object);
        $objectName = $this->admin->toString($object);

        if (!$object->getUser()) {
            $this->addWarningFlash($this->trans(
                id: 'SOURCE_ENTITY.MESSAGE.GRAB_ERROR_NO_USER',
                parameters: ['%object%' => $this->escapeHtml($objectName)],
                domain: 'admin-messages',
            ));

            return $this->redirectToList();
        }

        try {
            /** @var GrabSourceResultDTO $result */
            $result = $this->messageBusHandler->handleCommand(
                new GrabSourceCommand(
                    userId: $object->getUser()->getId(),
                    sourceId: $object->getId()
                ));
            $counterVacancyCreated = $result->vacanciesCreated;
            $counterVacancyUpdated = $result->vacanciesUpdated;

            if ($counterVacancyCreated > 0 || $counterVacancyUpdated > 0) {
                $this->addSuccessFlash($this->trans(
                    id: 'SOURCE_ENTITY.MESSAGE.GRAB_SUCCESS',
                    parameters: [
                        '%object%'          => $this->escapeHtml($objectName),
                        '%counter_created%' => $counterVacancyCreated,
                        '%counter_updated%' => $counterVacancyUpdated,
                    ],
                    domain: 'admin-messages',
                ));

                $filter = ['user' => ['value' => $object->getUser()?->getId()?->toString()]];

                return $this->redirectToRoute('admin.grab_log_list', ['filter' => $filter]);
            }

            $this->addWarningFlash($this->trans(
                id: 'SOURCE_ENTITY.MESSAGE.GRAB_SUCCESS_NO_VACANCY_CREATED',
                parameters: ['%object%' => $this->escapeHtml($objectName)],
                domain: 'admin-messages',
            ));

            return $this->redirectToList();
        } catch (HandlerFailedException $e) {
            $this->addErrorFlash($e->getPrevious()?->getMessage() ?? $this->trans(
                id: 'SOURCE_ENTITY.MESSAGE.GRAB_ERROR',
                parameters: ['%object%' => $this->escapeHtml($objectName)],
                domain: 'admin-messages',
            ));

            return $this->redirectToList();
        }
    }
}
