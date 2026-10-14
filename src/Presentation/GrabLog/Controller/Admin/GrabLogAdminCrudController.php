<?php

declare(strict_types=1);

namespace App\Presentation\GrabLog\Controller\Admin;

use App\Domain\User\Entity\User;
use App\Domain\Vacancy\Entity\Vacancy;
use App\Presentation\Shared\Admin\AbstractAdminCrudController;
use App\Presentation\Shared\AdminTrait\ConfigureFormFieldAssignedToUserTrait;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
            /** @var User $targetUser */
            $targetUser = $form->get('user')->getData();

            try {
                /** @var array<int, Vacancy> $selectedVacancies */
                $selectedVacancies = $selectedModelQuery->execute();

                foreach ($selectedVacancies as $vacancy) {
                    $vacancy->assignTo($targetUser);
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
}
