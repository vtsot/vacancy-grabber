<?php

declare(strict_types=1);

namespace App\Admin\Traits;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use function array_filter;
use function array_merge;
use function implode;

trait ConfigureFormTrait
{
    protected static array $choicesYesNo = [
        'CHOICE.YES' => true,
        'CHOICE.NO'  => false,
    ];

    protected function configureFormFieldChoice(
        FormMapper $form,
        string $name,
        array $choices = [],
        ?string $label = null,
        ?string $help = null,
        bool $required = false,
        bool $expanded = false,
        bool $multiple = false,
        ?array $options = [],
    ): void {
        $form->add(
            $name,
            ChoiceType::class,
            array_merge(
                [
                    'label'              => $label ?? 'FIELD.CHOICE',
                    'help'               => $help ?? '',
                    'choices'            => $choices,
                    'translation_domain' => $this->getTranslationDomain(),
                    'required'           => $required,
                    'expanded'           => $expanded,
                    'multiple'           => $multiple,
                ],
                $options
            )
        );
    }

    protected function configureFormFieldEnum(
        FormMapper $form,
        string $name,
        string $class,
        ?string $label = null,
        ?string $help = null,
        bool $required = true,
        ?array $options = [],
    ): void {
        $form->add(
            $name,
            EnumType::class,
            array_merge(
                [
                    'class'              => $class,
                    'label'              => $label,
                    'help'               => $help ?? '',
                    'translation_domain' => $this->getTranslationDomain(),
                    'required'           => $required,
                ],
                $options
            )
        );
    }

    protected function configureFormFieldYesNo(
        FormMapper $form,
        string $name,
        ?string $label = null,
        ?string $help = null,
        bool $required = true,
    ): void {
        $label ??= 'FIELD.YES_NO';
        $this->configureFormFieldChoice($form, $name, self::$choicesYesNo, $label, $help, $required);
    }

    protected function configureFormFieldActive(
        FormMapper $form,
        ?string $name = null,
        ?string $label = null,
        ?string $help = null,
    ): void {
        $name  ??= 'active';
        $label ??= 'FIELD.ACTIVE';
        $this->configureFormFieldYesNo($form, $name, $label, $help);
    }

    protected function configureFormFieldText(
        FormMapper $form,
        string $name,
        ?string $label = null,
        ?string $help = null,
        bool $required = false,
        array $options = [],
    ): void {
        $form->add(
            $name,
            TextType::class,
            array_merge(
                [
                    'required'           => $required,
                    'label'              => $label,
                    'help'               => $help,
                    'translation_domain' => $this->getTranslationDomain(),
                ],
                $options
            )
        );
    }

    protected function configureFormFieldNumber(
        FormMapper $form,
        string $name,
        ?string $label = null,
        ?string $help = null,
        bool $required = false,
        array $options = [],
    ): void {
        $form->add(
            $name,
            NumberType::class,
            array_merge(
                [
                    'required'           => $required,
                    'label'              => $label,
                    'help'               => $help,
                    'translation_domain' => $this->getTranslationDomain(),
                ],
                $options
            )
        );
    }

    protected function configureFormFieldInteger(
        FormMapper $form,
        string $name,
        ?string $label = null,
        ?string $help = null,
        bool $required = false,
        array $options = [],
    ): void {
        $form->add(
            $name,
            IntegerType::class,
            array_merge(
                [
                    'required'           => $required,
                    'label'              => $label,
                    'help'               => $help,
                    'translation_domain' => $this->getTranslationDomain(),
                ],
                $options
            )
        );
    }

    protected function configureFormFieldPosition(
        FormMapper $form,
        ?string $name = null,
        ?string $label = null,
        ?string $help = null,
        bool $required = false,
        array $options = [],
    ): void {
        $this->configureFormFieldNumber(
            $form,
            $name ?? 'position',
            $label ?? 'FIELD.POSITION',
            $help,
            $required,
            $options
        );
    }

    protected function configureFormFieldTextarea(
        FormMapper $form,
        string $name,
        ?string $label = null,
        ?string $help = null,
        bool $required = false,
        int $rows = 10,
        array $options = [],
    ): void {
        $form->add(
            $name,
            TextareaType::class,
            array_merge(
                [
                    'label'              => $label,
                    'help'               => $help,
                    'required'           => $required,
                    'attr'               => ['rows' => $rows],
                    'translation_domain' => $this->getTranslationDomain(),
                ],
                $options
            )
        );
    }

    protected function configureFormFieldDate(
        FormMapper $form,
        string $name,
        ?string $label = null,
        ?string $help = null,
        bool $required = false,
        array $options = [],
    ): void {
        $form->add(
            $name,
            DateType::class,
            array_merge(
                [
                    'required'           => $required,
                    'label'              => $label,
                    'help'               => $help,
                    'translation_domain' => $this->getTranslationDomain(),
                    'widget'             => 'single_text',
                    'attr'               => ['style' => 'width: 230px'],
                ],
                $options
            )
        );
    }

    protected function configureFormFieldTime(
        FormMapper $form,
        string $name,
        ?string $label = null,
        ?string $help = null,
        bool $required = false,
    ): void {
        $options = [
            'label'              => $label,
            'help'               => $help,
            'required'           => $required,
            'attr'               => ['style' => 'width: 220px;'],
            'translation_domain' => $this->getTranslationDomain(),
        ];
        $form->add($name, null, $options);
    }

    protected function configureFormFieldReplacements(
        FormMapper $form,
        ?string $name = null,
        ?string $data = null,
        ?string $label = null,
        ?string $help = null,
        ?int $rows = null,
    ): void {
        $name  ??= 'replacements';
        $label ??= 'FIELD_REPLACEMENTS.LABEL';
        $help  ??= 'FIELD_REPLACEMENTS.HELP';
        $data  ??= '';
        $rows  ??= 7;

        $form
            ->add(
                $name,
                TextareaType::class,
                [
                    'label'              => $label,
                    'help'               => $help,
                    'required'           => false,
                    'attr'               => ['rows' => $rows, 'readonly' => 'readonly'],
                    'data'               => $data,
                    'mapped'             => false,
                    'translation_domain' => $this->getTranslationDomain(),
                    // 'disabled' => true,
                ]
            );
    }

    protected function configureFormFieldElFinder(
        FormMapper $form,
        string $name,
        ?string $label = null,
        bool $required = false,
    ): void {
        $form->add(
            $name,
            ElFinderType::class,
            [
                'label'    => $label,
                'required' => $required,
                'instance' => 'form',
                'attr'     => ['class' => 'form-control'],
                'enable'   => true,
            ]
        );
    }

    protected function configureFormFieldLinkEntity(
        FormMapper $form,
        ?string $fieldsPrefix = null,
        ?array $labels = [],
    ): void {
        // fields names
        $fieldsPrefix ??= 'link';
        $nameLinkUrl          = ($fieldsPrefix ?? null) . 'Url';
        $nameLinkType         = ($fieldsPrefix ?? null) . 'Type';
        $nameLinkPage         = ($fieldsPrefix ?? null) . 'Page';
        $nameLinkCourseFilter = ($fieldsPrefix ?? null) . 'CourseFilter';

        // fields maps
        $mapExternal     = [$nameLinkUrl];
        $mapPage         = [$nameLinkPage];
        $mapCourseFilter = [$nameLinkCourseFilter];

        $form
            ->add(
                $nameLinkType,
                ChoiceFieldMaskType::class,
                [
                    'label'    => $labels['linkType'] ?? 'FIELD.LINK_ENTITY.TYPE',
                    'help'     => $labels['linkTypeHelp'] ?? null,
                    'required' => false,
                    'choices'  => [
                        'FIELD.LINK_ENTITY.CHOICE.LINK'          => LinkEntityInterface::LINK_TYPE_EXTERNAL_LINK,
                        'FIELD.LINK_ENTITY.CHOICE.PAGE'          => LinkEntityInterface::LINK_TYPE_PAGE,
                        'FIELD.LINK_ENTITY.CHOICE.COURSE_FILTER' => LinkEntityInterface::LINK_TYPE_COURSE_FILTER,
                    ],
                    'map' => [
                        LinkEntityInterface::LINK_TYPE_EXTERNAL_LINK => $mapExternal,
                        LinkEntityInterface::LINK_TYPE_PAGE          => $mapPage,
                        LinkEntityInterface::LINK_TYPE_COURSE_FILTER => $mapCourseFilter,
                    ],
                    'placeholder'        => 'FIELD.LINK_ENTITY.CHOICE.NONE',
                    'translation_domain' => $this->getTranslationDomain(),
                ]
            );

        $form
            ->add(
                $nameLinkPage,
                ModelType::class,
                [
                    'label'    => $labels['linkPage'] ?? 'FIELD.LINK_ENTITY.PAGE',
                    'help'     => $labels['linkPageHelp'] ?? 'FIELD.LINK_ENTITY.PAGE_HELP',
                    'required' => false,
                    'btn_add'  => false,
                ],
                ['admin_code' => 'admin.page']
            );

        $form
            ->add(
                $nameLinkCourseFilter,
                ModelType::class,
                [
                    'label'    => $labels['linkCourseFilter'] ?? 'FIELD.LINK_ENTITY.COURSE_FILTER',
                    'help'     => $labels['linkCourseFilterHelp'] ?? 'FIELD.LINK_ENTITY.COURSE_FILTER_HELP',
                    'required' => false,
                    'btn_add'  => false,
                ],
                ['admin_code' => 'admin.course_filter_preset']
            );

        $form
            ->add(
                $nameLinkUrl,
                TextType::class,
                [
                    'label'              => $labels['linkUrl'] ?? 'FIELD.LINK_ENTITY.LINK_URL',
                    'help'               => $labels['linkHelp'] ?? 'FIELD.LINK_ENTITY.LINK_URL_HELP',
                    'required'           => false,
                    'translation_domain' => $this->getTranslationDomain(),
                ]
            );
    }

    protected function configureFormFieldVideo(
        FormMapper $form,
        ?string $name = null,
        ?string $label = null,
        ?string $help = null,
        bool $required = false,
        array $options = [],
    ): void {
        $name  ??= 'video';
        $label ??= 'VIDEO_ENTITY.LABEL.VIDEO';
        $help  ??= 'VIDEO_ENTITY.HELP.VIDEO';

        $form
            ->add(
                $name,
                ModelAutocompleteType::class,
                array_merge(
                    [
                        'property'           => 'name',
                        'attr'               => ['data-sonata-select2-allow-clear' => 'true'],
                        'label'              => $label,
                        'help'               => $help,
                        'required'           => $required,
                        'btn_add'            => false,
                        'empty_data'         => [],
                        'multiple'           => false,
                        'to_string_callback' => function (/** @var Video $entity */ $entity) {
                            $labels   = [];
                            $labels[] = $entity->translate(LocaleVO::fromString('en_GB'))->getTitle();
                            $labels[] = '(' . $entity->getName() . ')';

                            return implode(' ', array_filter($labels));
                        },
                    ],
                    $options
                ),
                [
                    'admin_code' => 'admin.video.converted',
                ],
            );
    }
}
