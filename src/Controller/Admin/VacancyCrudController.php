<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Vacancy;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class VacancyCrudController extends AbstractCrudController
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
}
