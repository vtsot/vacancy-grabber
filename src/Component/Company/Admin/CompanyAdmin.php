<?php

declare(strict_types=1);

namespace App\Component\Company\Admin;

use App\Component\Company\Entity\Company;
use App\Component\General\Shared\AbstractAdmin;
use App\Component\General\Shared\AdminTrait\ConfigureAdminFullTrait;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method Company|null getSubject()
 */
class CompanyAdmin extends AbstractAdmin
{
    use ConfigureAdminFullTrait;

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        //$collection->remove('delete');
    }

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        //        $filterValues['interested'] = [
        //            'type'  => null,
        //            'value' => '1',
        //        ];
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
        $filter->add(
            'interested',
            CallbackFilter::class,
            [
                'label'         => 'COMPANY_ENTITY.LABEL.INTERESTED',
                'field_type'    => ChoiceType::class,
                'field_options' => [
                    'choices'            => self::$choicesYesNo,
                    'translation_domain' => $this->getTranslationDomain(),
                ],
                'callback' => static function (
                    ProxyQuery|QueryBuilder $qb,
                    string $alias,
                    string $field,
                    FilterData $data,
                ) {
                    if ($data->hasValue()) {
                        if ($data->getValue()) {
                            $qb
                                ->leftJoin($alias . '.notes', 'c_n')
                                ->leftJoin($alias . '.vacancies', 'v')
                                ->leftJoin('v.notes', 'v_n')
                                ->andWhere($qb->expr()->orX(
                                    $qb->expr()->isNotNull('c_n'),
                                    $qb->expr()->isNotNull('v_n'),
                                    $qb->expr()->isNotNull('v.resumeSentAt'),
                                ));
                        } else {
                            $qb
                                ->leftJoin($alias . '.notes', 'c_n')
                                ->leftJoin($alias . '.vacancies', 'v')
                                ->leftJoin('v.notes', 'v_n')
                                ->andWhere($qb->expr()->orX(
                                    $qb->expr()->isNull('c_n'),
                                    $qb->expr()->isNull('v_n'),
                                    $qb->expr()->isNull('v.resumeSentAt'),
                                ));
                        }

                        return true;
                    }

                    return false;
                },
            ]
        );

        $filter
            ->add('notes.description', null, ['label' => 'NOTE_ENTITY.LABEL.DESCRIPTION'])
            ->add('name', null, ['label' => 'COMPANY_ENTITY.LABEL.NAME'])
            ->add('url', null, ['label' => 'COMPANY_ENTITY.LABEL.URL']);

        $this->configureFilterFieldCreatedAt($filter);
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
                'template'      => '@company_templates/admin/CRUD/list__company.html.twig',
            ]
        );

        $this->configureListFieldText(
            $list,
            'notes-virtual',
            'COMPANY_ENTITY.LABEL.NOTE',
            [
                'virtual_field' => true,
                'template'      => '@company_templates/admin/CRUD/list__notes.html.twig',
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
