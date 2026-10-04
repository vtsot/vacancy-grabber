<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Controller\Admin;

use App\Component\General\Shared\AbstractAdminCrudController;
use App\Component\User\Contract\Entity\UserInterface;
use App\Component\User\Entity\User;
use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\Entity\Vacancy;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Throwable;

use function array_filter;
use function implode;

class VacancyAdminCrudController extends AbstractAdminCrudController
{
    public function batchActionAssign(ProxyQueryInterface $selectedModelQuery, Request $request): Response
    {
        $this->admin->checkAccess('assign');

        $csrfToken = $this->getCsrfToken('sonata.batch');

        $form = $this->createFormBuilder()
            ->add('user', ModelAutocompleteType::class, [
                'label'                => 'VACANCY_ENTITY.LABEL.USER',
                'translation_domain'   => 'admin-messages',
                'class'                => User::class,
                'model_manager'        => $this->admin->getModelManager(),
                'admin_code'           => $this->admin->getCode(),
                'property'             => ['email', 'firstName', 'lastName'],
                'required'             => false,
                'constraints'          => [new NotBlank()],
                'multiple'             => false,
                'minimum_input_length' => 3,
                'to_string_callback'   => static function (/** @var UserInterface $entity */ $entity) {
                    $labels   = [];
                    $labels[] = (string) $entity;

                    return implode(' ', array_filter($labels));
                },
            ])
            ->getForm();

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
