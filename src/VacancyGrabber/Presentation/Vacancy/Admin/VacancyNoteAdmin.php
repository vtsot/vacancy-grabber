<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Vacancy\Admin;

use App\VacancyGrabber\Domain\Note\Entity\Note;
use App\VacancyGrabber\Presentation\Shared\Admin\AbstractAdmin;
use App\VacancyGrabber\Presentation\Shared\AdminTrait\ConfigureAdminFullTrait;
use App\VacancyGrabber\Presentation\User\Admin\Traits\ConfigureFormAutocompleteUserTrait;
use Carbon\CarbonImmutable;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method Note|null getSubject()
 */
class VacancyNoteAdmin extends AbstractAdmin
{
    use ConfigureAdminFullTrait;
    use ConfigureFormAutocompleteUserTrait;

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

        $this->configureFormFieldAutocompleteUser($form);

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
