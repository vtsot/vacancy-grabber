<?php

declare(strict_types=1);

namespace App\Admin;

use App\Admin\Traits\ConfigureAdminFullTrait;
use App\Entity\Company;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
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
            '_sort_order' => 'ASC',
            '_sort_by'    => 'name',
        ];
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('name', null, ['label' => 'COMPANY_ENTITY.LABEL.NAME'])
            ->add('url', null, ['label' => 'COMPANY_ENTITY.LABEL.URL']);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $this->configureListFieldText(
            $list,
            'name',
            'COMPANY_ENTITY.LABEL.NAME',
            ['identifier' => true, 'route' => ['name' => 'edit']]
        );
        $this->configureListFieldUrl($list, 'url', 'COMPANY_ENTITY.LABEL.URL');
        $this->configureListFieldActions(list: $list, width: '200px');
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
            );

        $form->end();
    }
}
