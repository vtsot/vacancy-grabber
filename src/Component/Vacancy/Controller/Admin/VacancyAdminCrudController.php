<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Controller\Admin;

use App\Component\General\Shared\AbstractAdminCrudController;
use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\Entity\Vacancy;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class VacancyAdminCrudController extends AbstractAdminCrudController
{
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
