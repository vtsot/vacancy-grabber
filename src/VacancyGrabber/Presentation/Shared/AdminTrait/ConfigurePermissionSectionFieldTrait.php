<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Shared\AdminTrait;

use App\Admin\Traits\ContentPermissionType;
use App\Admin\Traits\LocaleVO;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

trait ConfigurePermissionSectionFieldTrait
{
    protected function configureFormSectionPermissions(FormMapper $form, string $class = 'col-md-12'): void
    {
        $form->with('GENERAL.SECTION.PERMISSIONS', ['class' => $class]);
        $this->configureFormPermissionsFields($form);
        $form->end();
    }

    protected function configureFormPermissionsFields(FormMapper $form): void
    {
        $form
            ->add(
                'permissionType',
                ChoiceFieldMaskType::class,
                [
                    'label'              => 'GENERAL.LABEL.PERMISSION_TYPE',
                    'translation_domain' => $this->getTranslationDomain(),
                    'choices'            => [
                        'GENERAL.PERMISSION_TYPE_CHOICE.ALL'               => ContentPermissionType::All->value,
                        'GENERAL.PERMISSION_TYPE_CHOICE.NOT_LOGGED_IN'     => ContentPermissionType::NotLoggedIn->value,
                        'GENERAL.PERMISSION_TYPE_CHOICE.REGISTERED'        => ContentPermissionType::Registered->value,
                        'GENERAL.PERMISSION_TYPE_CHOICE.WITH_SUBSCRIPTION' => ContentPermissionType::WithSubscription->value,
                    ],
                    'map' => [
                        ContentPermissionType::All->value              => [],
                        ContentPermissionType::Registered->value       => [],
                        ContentPermissionType::WithSubscription->value => ['subscriptionPackages'],
                    ],
                    'placeholder' => 'GENERAL.PLACEHOLDER.PERMISSION_TYPE',
                    'required'    => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ],
            );

        $form->add(
            'subscriptionPackages',
            ModelType::class,
            [
                'attr' => [
                    'data-sonata-select2-allow-clear' => 'true',
                ],
                'label'         => 'GENERAL.LABEL.SUBSCRIPTIONS',
                'required'      => false,
                'btn_add'       => false,
                'empty_data'    => [],
                'multiple'      => true,
                'choice_loader' => new CallbackChoiceLoader(fn () => $this->subscriptionPackageRepository->getChoiceList(
                    LocaleVO::fromString($this->localeProvider->getDefaultLocale()),
                    true
                )),
                'constraints' => [
                    new Callback([$this, 'validateSubscriptionsCount']),
                ],
            ],
            [
                'admin_code' => 'admin.regular_subscription_package',
            ]
        );
    }

    public function validateSubscriptionsCount($collection, ExecutionContextInterface $context): void
    {
        if ($context->getRoot()->get('permissionType')->getData() === ContentPermissionType::WithSubscription->value && $collection->count() === 0) {
            $context
                ->buildViolation('GENERAL.ERROR.PERMISSION_TYPE.SUBSCRIPTIONS_MIN_MESSAGE')
                ->setTranslationDomain($this->getTranslationDomain())
                ->atPath('subscriptionPackages')->addViolation();
        }
    }
}
