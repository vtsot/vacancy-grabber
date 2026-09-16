<?php

declare(strict_types=1);

namespace App\Component\Company\Admin;

use App\Component\Common\Shared\AbstractAdmin;
use App\Component\Common\Shared\AdminTrait\ConfigureAdminFullTrait;
use App\Component\Note\Entity\Note;
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
        $form->with($this->trans('NOTE_ENTITY.SECTION.NOTE'));

        $this->configureFormFieldDate(
            form: $form,
            name: 'notedAt',
            label: 'NOTE_ENTITY.LABEL.NOTED_AT',
            options: [
                'empty_data'  => new CarbonImmutable(),
                'constraints' => [new NotBlank()],
            ],
        );

        $this->configureFormFieldTextarea(
            form: $form,
            name: 'description',
            label: 'NOTE_ENTITY.LABEL.DESCRIPTION',
            rows: 7,
            options: ['constraints' => [new NotBlank()]],
        );

        $form->end();
    }
}
