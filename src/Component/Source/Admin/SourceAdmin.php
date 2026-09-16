<?php

declare(strict_types=1);

namespace App\Component\Source\Admin;

use App\Component\Common\Shared\AbstractAdmin;
use App\Component\Common\Shared\AdminTrait\ConfigureAdminFullTrait;
use App\Component\Source\Entity\Source;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

/**
 * @method Source|null getSubject()
 */
class SourceAdmin extends AbstractAdmin
{
    use ConfigureAdminFullTrait;

    protected function getAccessMapping(): array
    {
        return [
            'grab' => 'GRAB',
        ];
    }

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        //        $filterValues['active'] = [
        //            'type'  => null,
        //            'value' => '1',
        //        ];
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection->remove('delete');
        $collection->add('grab', $this->getRouterIdParameter() . '/grab');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('grabber', null, ['label' => 'SOURCE_ENTITY.LABEL.GRABBER'])
            ->add('name', null, ['label' => 'SOURCE_ENTITY.LABEL.NAME'])
            ->add('url', null, ['label' => 'SOURCE_ENTITY.LABEL.URL']);

        $this->configureFilterFieldActive($filter);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $this->configureListFieldActive($list);
        $this->configureListFieldText($list, 'name', 'SOURCE_ENTITY.LABEL.NAME', ['identifier' => true, 'route' => ['name' => 'edit']]);
        $this->configureListFieldUrl($list, 'url', 'SOURCE_ENTITY.LABEL.URL', ['attributes' => ['target' => '_blank']]);

        $this->configureListFieldActions($list, [
            'grab'   => ['template' => '@source_templates/admin/CRUD/list__action_grab.html.twig'],
            'edit'   => [],
            'delete' => [],
        ], [], '200px');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->trans('SOURCE_ENTITY.SECTION.MAIN'));

        $this->configureFormFieldActive($form);

        $form
            ->add('grabber', null, ['label' => 'SOURCE_ENTITY.LABEL.GRABBER'])
            ->add(
                'name',
                TextType::class,
                [
                    'label'       => $this->trans('SOURCE_ENTITY.LABEL.NAME'),
                    'required'    => false,
                    'constraints' => [new NotBlank()],
                ],
            )
            ->add(
                'url',
                TextType::class,
                [
                    'label'       => $this->trans('SOURCE_ENTITY.LABEL.URL'),
                    'required'    => false,
                    'constraints' => [new NotBlank(), new Url()],
                ],
            );

        $form->end();
    }
}
