<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Source\Controller\Admin;

use App\VacancyGrabber\Application\Source\Message\Command\GrabSourceCommand;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Infrastructure\Messenger\MessageBusHandler;
use App\VacancyGrabber\Presentation\Shared\Admin\AbstractAdminCrudController;
use Carbon\CarbonImmutable;
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
            $counterVacancyCreated = $this->messageBusHandler->handleCommand(
                new GrabSourceCommand(
                    userId: $object->getUser()->getId(),
                    sourceId: $object->getId()
                ));

            if ($counterVacancyCreated === 0) {
                $this->addWarningFlash($this->trans(
                    id: 'SOURCE_ENTITY.MESSAGE.GRAB_SUCCESS_NO_VACANCY_CREATED',
                    parameters: ['%object%' => $this->escapeHtml($objectName)],
                    domain: 'admin-messages',
                ));

                return $this->redirectToList();
            }

            $this->addSuccessFlash($this->trans(
                id: 'SOURCE_ENTITY.MESSAGE.GRAB_SUCCESS',
                parameters: ['%object%' => $this->escapeHtml($objectName), '%counter%' => $counterVacancyCreated],
                domain: 'admin-messages',
            ));

            $today  = CarbonImmutable::today()->format('Y-m-d');
            $filter = ['createdAt' => ['value' => ['start' => $today, 'end' => $today]]];

            return $this->redirectToRoute('admin.grab_log_list', ['filter' => $filter]);
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
