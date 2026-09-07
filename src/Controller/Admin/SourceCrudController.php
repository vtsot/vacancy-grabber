<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Source;
use App\Message\Command\Source\GrabSourceCommand;
use App\Service\MessageBusHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

use function assert;

class SourceCrudController extends AbstractCrudController
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

        try {
            $this->messageBusHandler->handleCommand(new GrabSourceCommand($object->getId()));

            $this->addSuccessFlash($this->trans(
                id: 'SOURCE_ENTITY.MESSAGE.GRAB_SUCCESS',
                parameters: ['%object%' => $this->escapeHtml($objectName)],
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
