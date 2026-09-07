<?php

declare(strict_types=1);

namespace App\Admin;

use App\Admin\Traits\ConfigureAdminFullTrait;
use App\Entity\Note;
use Carbon\CarbonImmutable;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method Note|null getSubject()
 */
class NoteAdmin extends AbstractAdmin
{
    use ConfigureAdminFullTrait;

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues = [
            '_sort_order' => 'DESC',
            '_sort_by'    => 'notedAt',
        ];
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('company', null, ['label' => 'NOTE_ENTITY.LABEL.COMPANY'])
            ->add('vacancy', null, ['label' => 'NOTE_ENTITY.LABEL.VACANCY'])
            ->add('description', null, ['label' => 'NOTE_ENTITY.LABEL.DESCRIPTION']);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $this->configureListFieldDate($list, 'notedAt', 'NOTE_ENTITY.LABEL.NOTED_AT');

        $this->configureListFieldText(
            $list,
            'company-virtual',
            'NOTE_ENTITY.LABEL.COMPANY',
            [
                'virtual_field' => true,
                'header_style'  => 'width:200px;',
                'template'      => 'admin/CRUD/note/list__company.html.twig',
            ]
        );

        $this->configureListFieldText(
            $list,
            'description-virtual',
            'NOTE_ENTITY.LABEL.DESCRIPTION',
            [
                'virtual_field' => true,
                'template'      => 'admin/CRUD/note/list__note.html.twig',
            ]
        );

        $this->configureListFieldActions(list: $list, width: '200px');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->trans('NOTE_ENTITY.SECTION.MAIN'));

        $this->configureFormFieldDate(
            form: $form,
            name: 'notedAt',
            label: 'NOTE_ENTITY.LABEL.NOTED_AT',
            options: ['data' => new CarbonImmutable(), 'constraints' => new NotBlank()],
        );

        $form->add('company', null, ['label' => 'VACANCY_ENTITY.LABEL.COMPANY']);
        $form->add('vacancy', null, ['label' => 'VACANCY_ENTITY.LABEL.VACANCY']);

        $this->configureFormFieldTextarea(
            form: $form,
            name: 'description',
            label: 'NOTE_ENTITY.LABEL.DESCRIPTION',
            rows: 5,
            options: ['constraints' => new NotBlank()],
        );
        $form->end();
    }
}
