<?php

declare(strict_types=1);

namespace App\Admin;

use App\Admin\Traits\ConfigureAdminFullTrait;
use App\Entity\Vacancy;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;

/**
 * @method Vacancy|null getSubject()
 */
class VacancyAdmin extends AbstractAdmin
{
    use ConfigureAdminFullTrait;

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            // ->add('id', null, ['label' => 'ID'])
            ->add('resumeSentAt', null, ['label' => 'VACANCY_ENTITY.LABEL.RESUME_SENT_AT'])
            ->add('source', null, ['label' => 'VACANCY_ENTITY.LABEL.SOURCE'])
            ->add('company', null, ['label' => 'VACANCY_ENTITY.LABEL.COMPANY'])
            ->add('externalId', null, ['label' => 'VACANCY_ENTITY.LABEL.EXTERNAL_ID'])
            ->add('salary', null, ['label' => 'VACANCY_ENTITY.LABEL.SALARY'])
            ->add('title', null, ['label' => 'VACANCY_ENTITY.LABEL.TITLE'])
            ->add('description', null, ['label' => 'VACANCY_ENTITY.LABEL.DESCRIPTION'])
            ->add('url', null, ['label' => 'VACANCY_ENTITY.LABEL.URL']);
    }

    protected function configureListFields(ListMapper $list): void
    {
        //        $list->add(
        //            'resumeSentAt',
        //            FieldDescriptionInterface::TYPE_DATE,
        //            [
        //                'label'        => 'VACANCY_ENTITY.LABEL.RESUME_SENT_AT',
        //                'header_style' => 'width:130px;',
        //            ],
        //        );

        $this->configureListFieldText(
            $list,
            'source-virtual',
            'VACANCY_ENTITY.LABEL.SOURCE',
            [
                'virtual_field' => true,
                'header_style'  => 'width:250px;',
                'template'      => 'admin/CRUD/vacancy/list__source.html.twig',
            ]
        );

        $this->configureListFieldText(
            $list,
            'description-virtual',
            'VACANCY_ENTITY.LABEL.DESCRIPTION',
            [
                'virtual_field' => true,
                'template'      => 'admin/CRUD/vacancy/list__description.html.twig',
            ]
        );

        $this->configureListFieldActions(list: $list, width: '200px');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->trans('VACANCY_ENTITY.SECTION.MAIN'));

        $this->configureFormFieldDate($form, 'resumeSentAt', 'VACANCY_ENTITY.LABEL.RESUME_SENT_AT');

        $form
            ->add(
                'notes',
                CollectionType::class,
                [
                    'label'        => 'VACANCY_ENTITY.LABEL.NOTES',
                    'required'     => false,
                    'by_reference' => false,
                    'type_options' => [
                        'delete'   => true,
                        'btn_add'  => true,
                        'required' => true,
                    ],
                ],
                [
                    'admin_code' => 'admin.vacancy.note',
                    'edit'       => 'inline',
                ],
            );

        $form->end();
    }
}
