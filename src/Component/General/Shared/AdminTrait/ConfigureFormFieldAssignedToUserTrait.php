<?php

declare(strict_types=1);

namespace App\Component\General\Shared\AdminTrait;

use App\Component\User\Contract\Entity\UserInterface;
use App\Component\User\Entity\User;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

use function array_merge;

trait ConfigureFormFieldAssignedToUserTrait
{
    protected function configureFormFieldAssignedToUser(
        FormBuilderInterface $formBuilder,
        ?string $name = null,
        ?string $label = null,
        ?array $options = [],
    ): FormBuilderInterface {
        $name  ??= 'user';
        $label ??= 'FIELD.NO_USER_ASSIGNED';

        $modelManager = $options['model_manager'] ?? $this->admin->getModelManager();
        $adminCode    = $options['admin_code'] ?? $this->admin->getCode();

        $formBuilder
            ->add($name, ModelAutocompleteType::class, array_merge([
                'label'                => $label,
                'translation_domain'   => 'admin-messages',
                'class'                => User::class,
                'model_manager'        => $modelManager,
                'admin_code'           => $adminCode,
                'property'             => ['email', 'firstName', 'lastName'],
                'required'             => false,
                'constraints'          => [new NotBlank()],
                'multiple'             => false,
                'minimum_input_length' => 3,
                'to_string_callback'   => static function (/** @var UserInterface $entity */ $entity) {
                    $labels   = [];
                    $labels[] = (string) $entity;

                    return implode(' ', array_filter($labels));
                },
            ], $options));

        return $formBuilder;
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
        array $options = [],
    ): void {
        $label ??= 'FIELD.YES_NO';
        $this->configureFormFieldChoice(
            form: $form,
            name: $name,
            choices: self::$choicesYesNo,
            label: $label,
            help: $help,
            required: $required,
            options: $options
        );
    }

    protected function configureFormFieldActive(
        FormMapper $form,
        ?string $name = null,
        ?string $label = null,
        ?string $help = null,
        bool $required = false,
        array $options = [],
    ): void {
        $name  ??= 'active';
        $label ??= 'FIELD.ACTIVE';
        $this->configureFormFieldYesNo($form, $name, $label, $help, $required, $options);
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
        $form->add($name, TextareaType::class, array_merge(
            [
                'label'              => $label,
                'help'               => $help,
                'required'           => $required,
                'attr'               => ['rows' => $rows],
                'translation_domain' => $this->getTranslationDomain(),
            ], $options
        ));
    }

    protected function configureFormFieldColor(
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
                    'attr'               => [
                        'style'   => 'width: 130px',
                        'pattern' => '^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$',
                        'class'   => 'color-picker',
                    ],
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

    // taken as example from rsm-admin
    protected function configureFormFieldLinkEntity(
        FormMapper $form,
        ?string $fieldsPrefix = null,
        ?array $labels = [],
        ?CallbackChoiceLoader $pageChoiceLoader = null,
        ?CallbackChoiceLoader $articleChoiceLoader = null,
        ?array $context = [],
    ): void {
        // fields names
        $fieldsPrefix ??= 'link';
        $nameLinkUrl     = ($fieldsPrefix ?? null) . 'Url';
        $nameLinkType    = ($fieldsPrefix ?? null) . 'Type';
        $nameLinkPage    = ($fieldsPrefix ?? null) . 'Page';
        $nameLinkTarget  = ($fieldsPrefix ?? null) . 'Target';
        $nameNoFollow    = ($fieldsPrefix ?? null) . 'NoFollow';
        $nameLinkArticle = ($fieldsPrefix ?? null) . 'Article';

        // fields maps
        $mapExternal = [$nameLinkUrl, $nameLinkTarget];
        $mapPage     = [$nameLinkPage, $nameLinkTarget];
        $mapNone     = [];
        $mapArticle  = [$nameLinkArticle, $nameLinkTarget];

        $form
            ->add(
                $nameLinkType,
                ChoiceFieldMaskType::class,
                [
                    'label'    => $labels['linkType'] ?? 'FIELD.LINK_ENTITY.TYPE',
                    'help'     => $labels['linkTypeHelp'] ?? null,
                    'required' => false,
                    'choices'  => [
                        'FIELD.LINK_ENTITY.CHOICE.NONE'    => LinkEntityInterface::LINK_TYPE_NONE,
                        'FIELD.LINK_ENTITY.CHOICE.LINK'    => LinkEntityInterface::LINK_TYPE_EXTERNAL_LINK,
                        'FIELD.LINK_ENTITY.CHOICE.PAGE'    => LinkEntityInterface::LINK_TYPE_PAGE,
                        'FIELD.LINK_ENTITY.CHOICE.ARTICLE' => LinkEntityInterface::LINK_TYPE_ARTICLE,
                    ],
                    'map' => [
                        LinkEntityInterface::LINK_TYPE_EXTERNAL_LINK => $mapExternal,
                        LinkEntityInterface::LINK_TYPE_PAGE          => $mapPage,
                        LinkEntityInterface::LINK_TYPE_NONE          => $mapNone,
                        LinkEntityInterface::LINK_TYPE_ARTICLE       => $mapArticle,
                    ],
                    'placeholder'        => 'FIELD.LINK_ENTITY.CHOICE.NONE',
                    'translation_domain' => $this->getTranslationDomain(),
                ]
            );

        $pageOptions = [
            'label'    => $labels['linkPage'] ?? 'FIELD.LINK_ENTITY.PAGE',
            'help'     => $labels['linkPageHelp'] ?? 'FIELD.LINK_ENTITY.PAGE_HELP',
            'required' => false,
            'btn_add'  => false,
        ];

        if ($pageChoiceLoader) {
            $pageOptions['choice_loader'] = $pageChoiceLoader;
        }

        $form
            ->add(
                $nameLinkPage,
                ModelType::class,
                $pageOptions,
                ['admin_code' => 'admin.page']
            );

        $articleOptions = [
            'label'    => $labels['linkArticle'] ?? 'FIELD.LINK_ENTITY.ARTICLE',
            'help'     => $labels['linkArticleHelp'] ?? 'FIELD.LINK_ENTITY.ARTICLE_HELP',
            'required' => false,
            'btn_add'  => false,
        ];

        if ($articleChoiceLoader) {
            $articleOptions['choice_loader'] = $articleChoiceLoader;
        }

        $form
            ->add(
                $nameLinkArticle,
                ModelType::class,
                $articleOptions,
                ['admin_code' => 'admin.article']
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

        $form
            ->add(
                $nameLinkTarget,
                ChoiceType::class,
                [
                    'label'    => $labels['linkTarget'] ?? 'FIELD.LINK_ENTITY.TARGET',
                    'help'     => $labels['linkTargetHelp'] ?? null,
                    'required' => false,
                    'choices'  => [
                        'FIELD.LINK_ENTITY.CHOICE.TOP'   => LinkEntityInterface::TARGET_TOP,
                        'FIELD.LINK_ENTITY.CHOICE.BLANK' => LinkEntityInterface::TARGET_BLANK,
                    ],
                    'placeholder'        => 'FIELD.LINK_ENTITY.CHOICE.NONE',
                    'translation_domain' => $this->getTranslationDomain(),
                ]
            );

        $form
            ->add(
                $nameNoFollow,
                ChoiceType::class,
                [
                    'label'   => $labels['noFollow'] ?? 'FIELD.LINK_ENTITY.NO_FOLLOW',
                    'help'    => $labels['noFollowHelp'] ?? 'FIELD.LINK_ENTITY.NO_FOLLOW_HELP',
                    'choices' => [
                        'FIELD.LINK_ENTITY.CHOICE.FOLLOW'    => false,
                        'FIELD.LINK_ENTITY.CHOICE.NO_FOLLOW' => true,
                    ],
                    'translation_domain' => $this->getTranslationDomain(),
                ]
            );
    }
}
