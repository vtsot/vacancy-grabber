<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Admin;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Applies to every admin: entity writes rejected by the domain become form errors.
 *
 * @extends AbstractAdminExtension<object>
 */
#[AutoconfigureTag('sonata.admin.extension', ['global' => true])]
final class DomainFormAdminExtension extends AbstractAdminExtension
{
    public function configureFormFields(FormMapper $form): void
    {
        $builder = $form->getFormBuilder();

        if (($mapper = $builder->getDataMapper()) !== null && !$mapper instanceof DomainAwareDataMapper) {
            $builder->setDataMapper(new DomainAwareDataMapper($mapper));
        }
    }
}
