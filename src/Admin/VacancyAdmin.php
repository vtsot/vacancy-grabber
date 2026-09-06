<?php

declare(strict_types=1);

namespace App\Admin;

use App\Admin\Traits\ConfigureAdminFullTrait;
use App\Entity\Vacancy;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Url;

/**
 * @method Vacancy|null getSubject()
 */
class VacancyAdmin extends AbstractAdmin
{
    use ConfigureAdminFullTrait;

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        //        $filterValues['interested'] = [
        //            'type'  => null,
        //            'value' => '1',
        //        ];
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add(
            'interested',
            CallbackFilter::class,
            [
                'label'         => 'VACANCY_ENTITY.LABEL.INTERESTED',
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
                                ->leftJoin($alias . '.notes', 'v_n')
                                ->leftJoin($alias . '.company', 'c')
                                ->leftJoin('c.notes', 'c_n')
                                ->andWhere($qb->expr()->orX(
                                    $qb->expr()->isNotNull('v_n'),
                                    $qb->expr()->isNotNull('c_n'),
                                    $qb->expr()->isNotNull($alias . '.resumeSentAt'),
                                ));
                        } else {
                            $qb
                                ->leftJoin($alias . '.notes', 'v_n')
                                ->leftJoin($alias . '.company', 'c')
                                ->leftJoin('c.notes', 'c_n')
                                ->andWhere($qb->expr()->orX(
                                    $qb->expr()->isNull('v_n'),
                                    $qb->expr()->isNull('c_n'),
                                    $qb->expr()->isNull($alias . '.resumeSentAt'),
                                ));
                        }

                        return true;
                    }

                    return false;
                },
            ]
        );

        $filter
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

        $form->add('company', null, ['label' => 'VACANCY_ENTITY.LABEL.COMPANY']);
        $this->configureFormFieldText($form, 'title', 'VACANCY_ENTITY.LABEL.TITLE');
        $this->configureFormFieldTextarea($form, 'description', 'VACANCY_ENTITY.LABEL.DESCRIPTION', null, false, 3);
        $this->configureFormFieldText($form, 'externalId', 'VACANCY_ENTITY.LABEL.EXTERNAL_ID');
        $this->configureFormFieldText($form, 'url', 'VACANCY_ENTITY.LABEL.URL', null, false, ['constraints' => [new Url()]]);
        $this->configureFormFieldText($form, 'salary', 'VACANCY_ENTITY.LABEL.SALARY');

        $form
            ->add(
                'notes',
                CollectionType::class,
                [
                    'label'        => 'VACANCY_ENTITY.LABEL.NOTE',
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
