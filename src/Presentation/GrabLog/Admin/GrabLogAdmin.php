<?php

declare(strict_types=1);

namespace App\Presentation\GrabLog\Admin;

use App\Domain\GrabLog\Entity\GrabLog;
use App\Presentation\Shared\Admin\AbstractAdmin;
use App\Presentation\Shared\AdminTrait\ConfigureAdminFullTrait;
use App\Presentation\User\Admin\Traits\ConfigureFilterAutocompleteUserTrait;
use App\Presentation\User\Admin\Traits\ConfigureFormAutocompleteUserTrait;
use App\Presentation\User\Admin\Traits\ConfigureListFieldUserTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * @method GrabLog|null getSubject()
 */
class GrabLogAdmin extends AbstractAdmin
{
    use ConfigureAdminFullTrait;
    use ConfigureListFieldUserTrait;
    use ConfigureFilterAutocompleteUserTrait;
    use ConfigureFormAutocompleteUserTrait;

    protected function getAccessMapping(): array
    {
        return [
            'assign' => 'ASSIGN',
        ];
    }

    protected function configureBatchActions(array $actions): array
    {
        return array_merge(['assign' => [
            'label'              => 'VACANCY_ENTITY.BATCH_ACTION.ASSIGN',
            'translation_domain' => $this->getTranslationDomain(),
            'ask_confirmation'   => false,
            'controller'         => 'App\Presentation\GrabLog\Controller\Admin\GrabLogAdminCrudController::batchActionAssign',
        ]], parent::configureBatchActions($actions));
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues = [
            '_sort_order' => 'DESC',
            '_sort_by'    => 'createdAt',
        ];
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $this->configureFilterFieldUser($filter);
        $this->configureFilterFieldNoUser($filter);

        $filter
            ->add('source', null, ['label' => 'GRAB_LOG_ENTITY.LABEL.SOURCE'])
            ->add('description', null, ['label' => 'GRAB_LOG_ENTITY.LABEL.DESCRIPTION']);

        $this->configureFilterFieldCreatedAtDateRange($filter);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $this->configureListFieldUser($list, 'user');

        $this->configureListFieldText(
            $list,
            'description-virtual',
            'GRAB_LOG_ENTITY.LABEL.DESCRIPTION',
            [
                'virtual_field' => true,
                'template'      => '@grab_log_templates/admin/CRUD/list__description.html.twig',
            ]
        );

        $this->configureListFieldCreatedAt($list);
        $this->configureListFieldActions(list: $list, width: '200px');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->trans('GRAB_LOG_ENTITY.SECTION.MAIN'));

        $this->configureFormFieldAutocompleteUser($form);
        $form->add('source', null, ['label' => 'GRAB_LOG_ENTITY.LABEL.SOURCE']);

        $this->configureFormFieldNumber($form, 'counterCompanyCreated', 'GRAB_LOG_ENTITY.LABEL.COUNTER_COMPANY_CREATED');
        $this->configureFormFieldNumber($form, 'counterVacancyCreated', 'GRAB_LOG_ENTITY.LABEL.COUNTER_VACANCY_CREATED');

        $this->configureFormFieldTextarea(
            form: $form,
            name: 'description',
            label: 'GRAB_LOG_ENTITY.LABEL.DESCRIPTION',
            rows: 5,
        );

        $form->end();
    }
}
