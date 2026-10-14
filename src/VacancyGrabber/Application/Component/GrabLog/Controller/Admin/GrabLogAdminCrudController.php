<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\GrabLog\Controller\Admin;

use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Application\Shared\Admin\AbstractAdminCrudController;
use App\VacancyGrabber\Application\Shared\AdminTrait\ConfigureFormFieldAssignedToUserTrait;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

// todo: remove the vacancy batch actions copied from VacancyAdminCrudController, only "assign" is used here
class GrabLogAdminCrudController extends AbstractAdminCrudController
{
    use ConfigureFormFieldAssignedToUserTrait;

    public function batchActionAssign(ProxyQueryInterface $selectedModelQuery, Request $request): Response
    {
        $this->admin->checkAccess('assign');

        $csrfToken = $this->getCsrfToken('sonata.batch');

        $formBuilder = $this->createFormBuilder();
        $form        = $this->configureFormFieldAssignedToUser($formBuilder)->getForm();
        $form->handleRequest($request);

        // submit form
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserInterface $targetUser */
            $targetUser = $form->get('user')->getData();

            try {
                /** @var array<int, Vacancy> $selectedVacancies */
                $selectedVacancies = $selectedModelQuery->execute();

                foreach ($selectedVacancies as $vacancy) {
                    $vacancy->setUser($targetUser);
                    $this->admin->update($vacancy);
                }

                // $this->entityManager->flush();

                $this->addSuccessFlash($this->trans(
                    id: 'VACANCY_ENTITY.BATCH_ACTION.ASSIGN_SUCCESS',
                    parameters: ['%user%' => (string) $targetUser],
                    domain: 'admin-messages',
                ));

                return $this->redirectToList();
            } catch (Throwable $e) {
                $this->addErrorFlash($this->trans(
                    id: 'VACANCY_ENTITY.BATCH_ACTION.ASSIGN_ERROR',
                    parameters: ['%user%' => (string) $targetUser],
                    domain: 'admin-messages',
                ));

                return $this->redirectToList();
            }
        }

        $action      = $request->request->get('action');
        $allElements = $request->request->getBoolean('all_elements');
        $idx         = $request->request->all('idx');
        $template    = '@vacancy_templates/admin/CRUD/batch_confirmation_assign.html.twig';

        return $this->renderWithExtraParams($template, [
            'action'                   => $request->request->get('action'),
            'form'                     => $form->createView(),
            'action_label'             => 'VACANCY_ENTITY.BATCH_ACTION.ASSIGN',
            'batch_translation_domain' => 'admin-messages',
            'csrf_token'               => $csrfToken,
            'data'                     => [
                'action'       => $action,
                'all_elements' => $allElements,
                'idx'          => $idx,
            ],
        ]);
    }

    public function batchActionViewed(ProxyQueryInterface $selectedModelQuery): RedirectResponse
    {
        /** @var array<int, Vacancy> $selectedVacancies */
        $selectedVacancies = $selectedModelQuery->execute();

        foreach ($selectedVacancies as $vacancy) {
            $vacancy->setViewed(true);
            $this->admin->update($vacancy);
        }

        $this->addSuccessFlash($this->trans(
            id: 'VACANCY_ENTITY.BATCH_ACTION.VIEWED_SUCCESS',
            domain: 'admin-messages',
        ));

        return $this->redirectToList();
    }

    public function batchActionPending(ProxyQueryInterface $selectedModelQuery): RedirectResponse
    {
        /** @var array<int, Vacancy> $selectedVacancies */
        $selectedVacancies = $selectedModelQuery->execute();

        foreach ($selectedVacancies as $vacancy) {
            $vacancy->setStatus(VacancyStatus::Pending);
            $this->admin->update($vacancy);
        }

        $this->addSuccessFlash($this->trans(
            id: 'VACANCY_ENTITY.BATCH_ACTION.PENDING_SUCCESS',
            domain: 'admin-messages',
        ));

        return $this->redirectToList();
    }

    public function batchActionInProgress(ProxyQueryInterface $selectedModelQuery): RedirectResponse
    {
        /** @var array<int, Vacancy> $selectedVacancies */
        $selectedVacancies = $selectedModelQuery->execute();

        foreach ($selectedVacancies as $vacancy) {
            $vacancy->setStatus(VacancyStatus::InProgress);
            $this->admin->update($vacancy);
        }

        $this->addSuccessFlash($this->trans(
            id: 'VACANCY_ENTITY.BATCH_ACTION.IN_PROGRESS_SUCCESS',
            domain: 'admin-messages',
        ));

        return $this->redirectToList();
    }

    public function batchActionDone(ProxyQueryInterface $selectedModelQuery): RedirectResponse
    {
        /** @var array<int, Vacancy> $selectedVacancies */
        $selectedVacancies = $selectedModelQuery->execute();

        foreach ($selectedVacancies as $vacancy) {
            $vacancy->setStatus(VacancyStatus::Done);
            $this->admin->update($vacancy);
        }

        $this->addSuccessFlash($this->trans(
            id: 'VACANCY_ENTITY.BATCH_ACTION.DONE',
            domain: 'admin-messages',
        ));

        return $this->redirectToList();
    }
}
