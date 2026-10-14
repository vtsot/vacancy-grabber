<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Shared\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\ModelManagerThrowable;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAdminCrudController extends CRUDController
{
    protected function addSuccessFlash(mixed $message): void
    {
        $this->addFlash('sonata_flash_success', $message);
    }

    protected function addErrorFlash(mixed $message): void
    {
        $this->addFlash('sonata_flash_error', $message);
    }

    protected function addWarningFlash(mixed $message): void
    {
        $this->addFlash('warning', $message);
    }

    /**
     * @see CRUDController::createAction()
     */
    public function customCreateAction(Request $request): Response
    {
        $this->assertObjectExists($request);

        $this->admin->checkAccess('create');

        $newObject = $this->admin->getNewInstance();

        $preResponse = $this->preCreate($request, $newObject);

        if ($preResponse !== null) {
            return $preResponse;
        }

        $this->admin->setSubject($newObject);

        $form = $this->admin->getForm();

        $form->setData($newObject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            if ($isFormValid) {
                $this->admin->setSubject($form->getData());

                try {
                    $newObject = $this->createSubmittedObject($form) ?? $newObject;

                    if ($request->get('btn_create_and_create') !== null) {
                        $params = [];

                        if ($this->admin->hasActiveSubClass()) {
                            $params['subclass'] = $request->get('subclass');
                        }

                        return new RedirectResponse(
                            $this->admin->generateUrl(
                                $this->admin->getCurrentRouteName(),
                                $params,
                            )
                        );
                    }

                    return $this->redirectTo($request, $newObject);
                } catch (ModelManagerThrowable $e) {
                    $errorMessage = $this->handleModelManagerThrowable($e);

                    $isFormValid = false;
                }
            }

            if (!$isFormValid) {
                $this->addErrorFlash(
                    $errorMessage ?? $this->trans(
                        'flash_create_error',
                        ['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
                        'SonataAdminBundle',
                    )
                );
            }
        }

        $formView = $form->createView();

        $this->setFormTheme($formView, $this->admin->getFormTheme());

        return $this->renderWithExtraParams('@general_templates/admin/CRUD/edit.html.twig', [
            'action'   => 'create',
            'form'     => $formView,
            'object'   => $newObject,
            'objectId' => null,
        ]);
    }

    /**
     * @throws ModelManagerThrowable
     */
    protected function createSubmittedObject(FormInterface $form): ?object
    {
        $newObject = $this->admin->create($form->getData());

        $this->addSuccessFlash(
            $this->trans(
                'flash_create_success',
                ['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
                'SonataAdminBundle',
            )
        );

        return $newObject;
    }
}
