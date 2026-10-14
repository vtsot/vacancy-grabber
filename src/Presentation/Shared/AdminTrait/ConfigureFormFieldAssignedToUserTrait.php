<?php

declare(strict_types=1);

namespace App\Presentation\Shared\AdminTrait;

use App\Domain\User\Entity\User;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
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
                'to_string_callback'   => static function (/** @var User $entity */ $entity) {
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
                    'translation_domain' => 'admin-messages',
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
                    'translation_domain' => 'admin-messages',
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
                    'translation_domain' => 'admin-messages',
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
                    'translation_domain' => 'admin-messages',
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
                'translation_domain' => 'admin-messages',
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
                    'translation_domain' => 'admin-messages',
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
                    'translation_domain' => 'admin-messages',
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
            'translation_domain' => 'admin-messages',
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
                    'translation_domain' => 'admin-messages',
                    // 'disabled' => true,
                ]
            );
    }
}
