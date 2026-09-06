<?php

declare(strict_types=1);

namespace App\Admin;

use App\Admin\Traits\ConfigureAdminFullTrait;
use App\Entity\Note;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;


/**
 * @method Note|null getSubject()
 */
class NoteAdmin extends AbstractAdmin
{
    use ConfigureAdminFullTrait;

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues = [
            '_sort_order' => 'ASC',
            '_sort_by'    => 'notedAt',
        ];
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('vacancy.company', null, ['label' => 'COMPANY_ENTITY.LABEL.COMPANY'])
            ->add('description', null, ['label' => 'VACANCY_ENTITY.LABEL.DESCRIPTION']);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $this->configureListFieldText($list, 'vacancy.company', 'COMPANY_ENTITY.LABEL.COMPANY', ['header_style'  => 'width:150px;']);
        $this->configureListFieldText($list, 'vacancy', 'VACANCY_ENTITY.LABEL.VACANCY', ['header_style'  => 'width:200px;']);
        $this->configureListFieldDate($list, 'notedAt', 'NOTE_ENTITY.LABEL.NOTED_AT');
        $this->configureListFieldText($list, 'description', 'NOTE_ENTITY.LABEL.DESCRIPTION');

        $this->configureListFieldActions(list: $list, width: '200px');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->trans('NOTE_ENTITY.SECTION.MAIN'));
        $this->configureFormFieldDate($form, 'notedAt', 'NOTE_ENTITY.LABEL.NOTED_AT');
        $this->configureFormFieldTextarea(form: $form, name: 'description', label: 'NOTE_ENTITY.LABEL.DESCRIPTION', rows: 5);
        $form->end();
    }
}
