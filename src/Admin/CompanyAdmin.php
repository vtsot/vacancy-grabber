<?php

declare(strict_types=1);

namespace App\Admin;

use App\Admin\Traits\ConfigureAdminFullTrait;
use App\Entity\Company;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method Company|null getSubject()
 */
class CompanyAdmin extends AbstractAdmin
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
            ->add('notes.description', null, ['label' => 'NOTE_ENTITY.LABEL.DESCRIPTION'])
            ->add('name', null, ['label' => 'COMPANY_ENTITY.LABEL.NAME'])
            ->add('url', null, ['label' => 'COMPANY_ENTITY.LABEL.URL']);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $this->configureListFieldText(
            $list,
            'name-virtual',
            'COMPANY_ENTITY.LABEL.NAME',
            [
                'virtual_field' => true,
                'header_style'  => 'width:300px;',
                'template'      => 'admin/CRUD/company/list__company.html.twig',
            ]
        );

        $this->configureListFieldText(
            $list,
            'notes-virtual',
            'COMPANY_ENTITY.LABEL.NOTE',
            [
                'virtual_field' => true,
                'template'      => 'admin/CRUD/company/list__notes.html.twig',
            ]
        );

        $this->configureListFieldCreatedAt($list);
        $this->configureListFieldActions(list: $list);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->trans('COMPANY_ENTITY.SECTION.MAIN'));

        $form
            ->add(
                'name',
                TextType::class,
                [
                    'label'       => $this->trans('COMPANY_ENTITY.LABEL.NAME'),
                    'required'    => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ],
            )
            ->add(
                'url',
                TextType::class,
                [
                    'label'       => $this->trans('COMPANY_ENTITY.LABEL.URL'),
                    'required'    => false,
                    'constraints' => [],
                ],
            );

        $form
            ->add(
                'notes',
                CollectionType::class,
                [
                    'label'        => 'COMPANY_ENTITY.LABEL.NOTE',
                    'required'     => false,
                    'by_reference' => false,
                    'type_options' => [
                        'delete'   => true,
                        'btn_add'  => true,
                        'required' => true,
                    ],
                ],
                [
                    'admin_code' => 'admin.company.note',
                    'edit'       => 'inline',
                ],
            );

        $form->end();
    }
}
