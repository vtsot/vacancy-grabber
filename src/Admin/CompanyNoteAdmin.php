<?php

declare(strict_types=1);

namespace App\Admin;

use App\Admin\Traits\ConfigureAdminFullTrait;
use App\Entity\Note;
use Carbon\CarbonImmutable;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method Note|null getSubject()
 */
class CompanyNoteAdmin extends AbstractAdmin
{
    use ConfigureAdminFullTrait;

    protected function configureFormFields(FormMapper $form): void
    {
        $this->configureFormFieldDate(
            form: $form,
            name: 'notedAt',
            label: 'NOTE_ENTITY.LABEL.NOTED_AT',
            options: [
                'data'        => new CarbonImmutable(),
                'constraints' => [new NotBlank()],
            ],
        );

        $this->configureFormFieldTextarea(
            form: $form,
            name: 'description',
            label: 'NOTE_ENTITY.LABEL.DESCRIPTION',
            rows: 3,
            options: ['constraints' => [new NotBlank()]],
        );
    }
}
