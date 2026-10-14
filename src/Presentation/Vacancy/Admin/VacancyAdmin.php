<?php

declare(strict_types=1);

namespace App\Presentation\Vacancy\Admin;

use App\Domain\Vacancy\Entity\Vacancy;
use App\Presentation\Shared\Admin\AbstractAdmin;
use App\Presentation\Shared\AdminTrait\ConfigureAdminFullTrait;
use App\Presentation\User\Admin\Traits\ConfigureFilterAutocompleteUserTrait;
use App\Presentation\User\Admin\Traits\ConfigureFormAutocompleteUserTrait;
use App\Presentation\User\Admin\Traits\ConfigureListFieldUserTrait;
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
    use ConfigureListFieldUserTrait;
    use ConfigureFilterAutocompleteUserTrait;
    use ConfigureFormAutocompleteUserTrait;

    protected function getAccessMapping(): array
    {
        return [
            'viewed' => 'VIEWED',
            'assign' => 'ASSIGN',
        ];
    }

    protected function configureBatchActions(array $actions): array
    {
        $actions = array_merge(['viewed' => [
            'label'              => 'VACANCY_ENTITY.BATCH_ACTION.VIEWED',
            'translation_domain' => $this->getTranslationDomain(),
            'ask_confirmation'   => true,
        ], 'pending' => [
            'label'              => 'VACANCY_ENTITY.BATCH_ACTION.PENDING',
            'translation_domain' => $this->getTranslationDomain(),
            'ask_confirmation'   => true,
        ], 'in-progress' => [
            'label'              => 'VACANCY_ENTITY.BATCH_ACTION.IN_PROGRESS',
            'translation_domain' => $this->getTranslationDomain(),
            'ask_confirmation'   => true,
        ], 'done' => [
            'label'              => 'VACANCY_ENTITY.BATCH_ACTION.DONE',
            'translation_domain' => $this->getTranslationDomain(),
            'ask_confirmation'   => true,
        ], 'assign' => [
            'label'              => 'VACANCY_ENTITY.BATCH_ACTION.ASSIGN',
            'translation_domain' => $this->getTranslationDomain(),
            'ask_confirmation'   => false,
            'controller'         => 'App\Application\Vacancy\Controller\Admin\VacancyAdminCrudController::batchActionAssign',
        ]], parent::configureBatchActions($actions));

        unset($actions['delete']);

        return $actions;
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
        $this->configureFilterFieldUser($filter);
        $this->configureFilterFieldNoUser($filter);

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
                                ->andWhere($qb->expr()->orX(
                                    $qb->expr()->isNotNull($alias . '.resumeSentAt'),
                                    $qb->expr()->isNotNull($alias . '.deadLineAt'),
                                ));
                        } else {
                            $qb
                                ->andWhere($qb->expr()->orX(
                                    $qb->expr()->isNull($alias . '.resumeSentAt'),
                                    $qb->expr()->isNull($alias . '.deadLineAt'),
                                ));
                        }

                        return true;
                    }

                    return false;
                },
            ]
        );

        $this->configureFilterFieldBoolean($filter, 'viewed', 'VACANCY_ENTITY.LABEL.VIEWED');
        $filter->add('status', null, ['label' => 'VACANCY_ENTITY.LABEL.STATUS']);
        $this->configureFilterFieldDateRange($filter, 'resumeSentAt', 'VACANCY_ENTITY.LABEL.RESUME_SENT_AT');
        $this->configureFilterFieldDateRange($filter, 'deadLineAt', 'VACANCY_ENTITY.LABEL.DEAD_LINE_AT');

        $filter
            ->add('source', null, ['label' => 'VACANCY_ENTITY.LABEL.SOURCE'])
            ->add('company', null, ['label' => 'VACANCY_ENTITY.LABEL.COMPANY'])
            ->add('externalId', null, ['label' => 'VACANCY_ENTITY.LABEL.EXTERNAL_ID'])
            ->add('salary', null, ['label' => 'VACANCY_ENTITY.LABEL.SALARY'])
            ->add('title', null, ['label' => 'VACANCY_ENTITY.LABEL.TITLE'])
            ->add('description', null, ['label' => 'VACANCY_ENTITY.LABEL.DESCRIPTION'])
            ->add('url', null, ['label' => 'VACANCY_ENTITY.LABEL.URL']);

        $this->configureFilterFieldCreatedAt($filter);
        $this->configureFilterFieldUpdatedAt($filter);
    }

    protected function configureListFields(ListMapper $list): void
    {
        // $this->configureListFieldUser($list, 'user');
        $this->configureListFieldText(
            $list,
            'source-virtual',
            'VACANCY_ENTITY.LABEL.SOURCE',
            [
                'virtual_field' => true,
                'header_style'  => 'width:250px;',
                'template'      => '@vacancy_templates/admin/CRUD/list__source.html.twig',
            ]
        );

        $this->configureListFieldActive($list, 'viewed', 'VACANCY_ENTITY.LABEL.VIEWED');
        // $this->configureListFieldEnum($list, 'status', 'VACANCY_ENTITY.LABEL.STATUS');

        $this->configureListFieldText(
            $list,
            'description-virtual',
            'VACANCY_ENTITY.LABEL.DESCRIPTION',
            [
                'virtual_field' => true,
                'template'      => '@vacancy_templates/admin/CRUD/list__description.html.twig',
            ]
        );

        $this->configureListFieldCreatedAt($list);
        $this->configureListFieldActions(list: $list, width: '200px');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->trans('VACANCY_ENTITY.SECTION.MAIN'));

        $this->configureFormFieldAutocompleteUser($form);
        $form->add('company', null, ['label' => 'VACANCY_ENTITY.LABEL.COMPANY']);
        $this->configureFormFieldText($form, 'title', 'VACANCY_ENTITY.LABEL.TITLE');
        $this->configureFormFieldTextarea($form, 'description', 'VACANCY_ENTITY.LABEL.DESCRIPTION', null, false, 3);
        $form->add('source', null, ['label' => 'VACANCY_ENTITY.LABEL.SOURCE']);
        $this->configureFormFieldText($form, 'externalId', 'VACANCY_ENTITY.LABEL.EXTERNAL_ID');
        $this->configureFormFieldText($form, 'url', 'VACANCY_ENTITY.LABEL.URL', null, false, ['constraints' => [new Url()]]);
        $this->configureFormFieldText($form, 'salary', 'VACANCY_ENTITY.LABEL.SALARY');

        $this->configureFormFieldActive($form, 'viewed', 'VACANCY_ENTITY.LABEL.VIEWED');
        $form->add('status', null, ['label' => 'VACANCY_ENTITY.LABEL.STATUS']);
        $this->configureFormFieldDate($form, 'resumeSentAt', 'VACANCY_ENTITY.LABEL.RESUME_SENT_AT');
        $this->configureFormFieldDate($form, 'deadLineAt', 'VACANCY_ENTITY.LABEL.DEAD_LINE_AT');

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
