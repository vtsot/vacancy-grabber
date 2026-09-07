<?php

declare(strict_types=1);

namespace App\Admin;

use App\Admin\Traits\ConfigureAdminFullTrait;
use App\Entity\GrabLog;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * @method GrabLog|null getSubject()
 */
class GrabLogAdmin extends AbstractAdmin
{
    use ConfigureAdminFullTrait;

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues = [
            '_sort_order' => 'DESC',
            '_sort_by'    => 'createdAt',
        ];
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('source', null, ['label' => 'GRAB_LOG_ENTITY.LABEL.SOURCE'])
            ->add('description', null, ['label' => 'GRAB_LOG_ENTITY.LABEL.DESCRIPTION']);

        $this->configureFilterFieldCreatedAtDateRange($filter);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $this->configureListFieldCreatedAt($list);
        $this->configureListFieldText(
            $list,
            'description-virtual',
            'GRAB_LOG_ENTITY.LABEL.DESCRIPTION',
            [
                'virtual_field' => true,
                'template'      => 'admin/CRUD/grab-log/list__description.html.twig',
            ]
        );

        $this->configureListFieldActions(list: $list, width: '200px');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->trans('GRAB_LOG_ENTITY.SECTION.MAIN'));

        $form->add('source', null, ['label' => 'GRAB_LOG_ENTITY.LABEL.SOURCE']);

        $this->configureFormFieldNumber($form, 'counterCompanyCreated', 'GRAB_LOG_ENTITY.LABEL.COUNTER_COMPANY_CREATED');
        $this->configureFormFieldNumber($form, 'counterCompanyUpdated', 'GRAB_LOG_ENTITY.LABEL.COUNTER_COMPANY_UPDATED');
        $this->configureFormFieldNumber($form, 'counterVacancyCreated', 'GRAB_LOG_ENTITY.LABEL.COUNTER_VACANCY_CREATED');
        $this->configureFormFieldNumber($form, 'counterVacancyUpdated', 'GRAB_LOG_ENTITY.LABEL.COUNTER_VACANCY_UPDATED');

        $this->configureFormFieldTextarea(
            form: $form,
            name: 'description',
            label: 'GRAB_LOG_ENTITY.LABEL.DESCRIPTION',
            rows: 5,
        );

        $form->end();
    }
}
