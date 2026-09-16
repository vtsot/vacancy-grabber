<?php

declare(strict_types=1);

namespace App\Component\Common\Shared\AdminTrait;

use Carbon\Carbon;
use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\DoctrineORMAdminBundle\Filter\BooleanFilter;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\DoctrineORMAdminBundle\Filter\NumberFilter;
use Sonata\Form\Type\DateRangeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use function array_filter;
use function array_merge;
use function implode;

trait ConfigureFilterTrait
{
    protected function configureFilterFieldDate(
        DatagridMapper $filter,
        string $name,
        ?string $label = null,
        array $options = [],
    ): void {
        $options['label'] = $label;

        $filter->add(
            $name,
            DateFilter::class,
            array_merge(
                [
                    'field_options' => ['widget' => 'single_text'],
                ],
                $options,
            ),
            [
                'datepicker_use_button' => false,
                // 'view_timezone'         => $this->getTimezone(), // search considering timezone
            ]
        );
    }

    protected function configureFilterFieldDateRange(
        DatagridMapper $filter,
        string $name,
        ?string $label = null,
        array $options = [],
    ): void {
        $options['label'] = $label;
        $filter
            ->add(
                $name,
                DateRangeFilter::class,
                array_merge(
                    [
                        'field_options' => ['field_options' => ['widget' => 'single_text']],
                    ],
                    $options
                )
            );
    }

    protected function configureFilterFieldCreatedAt(DatagridMapper $filter): void
    {
        $this->configureFilterFieldDate($filter, 'createdAt', 'FIELD.CREATED_AT');
    }

    protected function configureFilterFieldCreatedAtDateRange(DatagridMapper $filter): void
    {
        $this->configureFilterFieldDateRange($filter, 'createdAt', 'FIELD.CREATED_AT');
    }

    protected function configureFilterFieldUpdatedAt(DatagridMapper $filter): void
    {
        $this->configureFilterFieldDate($filter, 'updatedAt', 'FIELD.UPDATED_AT');
    }

    protected function configureFilterFieldUpdatedAtDateRange(DatagridMapper $filter): void
    {
        $this->configureFilterFieldDateRange($filter, 'updatedAt', 'FIELD.UPDATED_AT');
    }

    protected function configureFilterFieldText(
        DatagridMapper $filter,
        string $name,
        ?string $label = null,
        array $options = [],
    ): void {
        $options['label'] = $label;
        $filter->add($name, null, $options);
    }

    protected function configureFilterFieldActive(
        DatagridMapper $filter,
        ?string $name = null,
        ?string $label = null,
        array $options = [],
    ): void {
        $name  ??= 'active';
        $label ??= 'FIELD.ACTIVE';
        $options['label'] = $label;
        $filter->add($name, null, $options);
    }

    protected function configureFilterFieldChoice(
        DatagridMapper $filter,
        string $name,
        array $choices = [],
        ?string $label = null,
        array $options = [],
    ): void {
        $filter->add(
            $name,
            ChoiceFilter::class,
            array_merge([
                'label'         => $label ?? 'FIELD.CHOICE',
                'global_search' => true,
                'field_type'    => ChoiceType::class,
                'field_options' => [
                    'choices'            => $choices,
                    'translation_domain' => $this->getTranslationDomain(),
                ],
            ], $options),
        );
    }

    protected function configureFilterFieldNumber(
        DatagridMapper $filter,
        string $name,
        ?string $label = null,
        array $options = [],
    ): void {
        $options['label'] = $label;

        $filter->add($name, NumberFilter::class, $options);
    }

    protected function configureFilterFieldBoolean(
        DatagridMapper $filter,
        string $name,
        ?string $label = null,
        array $options = [],
    ): void {
        $options['label'] = $label;

        $filter->add($name, BooleanFilter::class, $options);
    }

    protected function configureFilterFieldModel(
        DatagridMapper $filter,
        string $name,
        string|callable|null $property,
        ?string $label = null,
        array $options = [],
    ): void {
        $options['label']                         = $label;
        $options['field_options']['choice_label'] = $property;

        $filter->add($name, ModelFilter::class, $options);
    }

    protected function configureFilterFieldUser(
        DatagridMapper $filter,
        ?string $name = null,
        ?string $label = null,
        ?array $options = [],
    ): void {
        $name  ??= 'user';
        $label ??= 'FIELD.USER';

        $filter->add(
            $name,
            ModelFilter::class,
            array_merge(
                [
                    'label'         => $label,
                    'field_type'    => ModelAutocompleteType::class,
                    'field_options' => [
                        'property'           => ['email', 'firstName', 'lastName'/* , 'discount.code' */],
                        'to_string_callback' => static function (/** @var User $entity */ $entity) {
                            $labels   = [];
                            $labels[] = $entity->getFullName();
                            $labels[] = ' (' . $entity->getEmail() . ')';

                            return implode(' ', array_filter($labels));
                        },
                    ],
                ],
                $options
            ),
            [
                'admin_code' => 'admin.user.all',
            ]
        );
    }

    protected function configureFilterFieldDiscount(
        DatagridMapper $filter,
        ?string $name = null,
        ?string $label = null,
        ?array $options = [],
    ): void {
        $name  ??= 'discount';
        $label ??= 'DISCOUNT_ENTITY.LABEL.DISCOUNT';

        $filter->add(
            $name,
            ModelFilter::class,
            array_merge(
                [
                    'label'         => $label,
                    'field_type'    => ModelAutocompleteType::class,
                    'field_options' => [
                        'property'             => ['code', 'salePerson.name'],
                        'minimum_input_length' => 2,
                        'to_string_callback'   => static function (/** @var Discount $entity */ $entity) {
                            $labels   = [];
                            $labels[] = $entity->getCode();
                            $labels[] = $entity->getType()->name;
                            $labels[] = $entity->getAvailable()?->name;
                            $labels[] = $entity->getSalePerson()?->getName();

                            return implode(' ', array_filter($labels));
                        },
                    ],
                ],
                $options
            ),
            [
                'admin_code' => 'admin.discount.all',
            ]
        );
    }

    protected function configureFilterFieldUserDiscountAt(
        DatagridMapper $filter,
        ?string $name = null,
        ?string $label = null,
        ?string $userAlias = null,
    ): void {
        $name  ??= 'discountAt';
        $label ??= 'USER_ENTITY.LABEL.DISCOUNT_AT';

        $filter->add(
            $name,
            CallbackFilter::class,
            [
                'label'         => $label,
                'field_type'    => DateRangeType::class,
                'field_options' => ['field_options' => ['widget' => 'single_text']],
                'callback'      => function (
                    ProxyQuery|QueryBuilder $qb,
                    string $defaultAlias,
                    string $field,
                    FilterData $data,
                ) use ($userAlias) {
                    if ($data->hasValue()
                        && ($startAt = $data->getValue()['start'] ?? null) instanceof DateTimeInterface
                        && ($endAt = $data->getValue()['end'] ?? null) instanceof DateTimeInterface
                    ) {
                        $alias = $userAlias ?? $defaultAlias;
                        $qb->andWhere($alias . '.discountAt >= :discountStartAt')
                            ->andWhere($alias . '.discountAt <= :discountEndAt')
                            ->setParameter('discountStartAt', Carbon::instance($startAt)->startOfDay())
                            ->setParameter('discountEndAt', Carbon::instance($endAt)->endOfDay());

                        return true;
                    }

                    return false;
                },
            ]
        );
    }

    protected function configureFilterFieldCreatedBy(
        DatagridMapper $filter,
        ?string $name = null,
        ?string $label = null,
        ?array $options = [],
    ): void {
        $name  ??= 'createdBy';
        $label ??= 'FIELD.CREATED_BY';

        $this->configureFilterFieldUser($filter, $name, $label, $options);
    }

    protected function configureFilterFieldUpdatedBy(
        DatagridMapper $filter,
        ?string $name = null,
        ?string $label = null,
        ?array $options = [],
    ): void {
        $name  ??= 'updatedBy';
        $label ??= 'FIELD.UPDATED_BY';

        $this->configureFilterFieldUser($filter, $name, $label, $options);
    }

    protected function configureFilterFieldVideo(
        DatagridMapper $filter,
        ?string $name = null,
        ?string $label = null,
        ?array $options = [],
    ): void {
        $name  ??= 'video';
        $label ??= 'VIDEO_ENTITY.LABEL.VIDEO';

        $filter->add(
            $name,
            ModelFilter::class,
            array_merge(
                [
                    'label'         => $label,
                    'field_type'    => ModelAutocompleteType::class,
                    'field_options' => [
                        'property'             => ['name'],
                        'minimum_input_length' => 3,
                        'to_string_callback'   => static function (/** @var Video $entity */ $entity) {
                            $labels   = [];
                            $labels[] = $entity->translate(LocaleVO::fromString('en_GB'))->getTitle();
                            $labels[] = '(' . $entity->getName() . ')';

                            return implode(' ', array_filter($labels));
                        },
                    ],
                ],
                $options
            ),
            [
                'admin_code' => 'admin.video.converted',
            ]
        );
    }
}
