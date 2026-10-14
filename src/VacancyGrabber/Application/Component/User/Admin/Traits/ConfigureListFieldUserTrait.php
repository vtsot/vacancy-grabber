<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\User\Admin\Traits;

use App\Contract\Entity\UserInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;

use function array_filter;
use function implode;

trait ConfigureListFieldUserTrait
{
    protected function configureListFieldUser(
        ListMapper $list,
        ?string $name = null,
        ?string $label = null,
        ?array $options = [],
    ): void {
        $name  ??= 'user';
        $label ??= 'USER_ENTITY.LABEL.USER';

        $list->add($name, FieldDescriptionInterface::TYPE_MANY_TO_ONE, array_merge([
            'label'               => $label,
            'associated_property' => static function (UserInterface $entity) {
                $labels   = [];
                $labels[] = (string) $entity;

                return implode(' ', array_filter($labels));
            },
            'admin_code' => 'admin.user.all',
            'route'      => ['name' => 'show'],
        ], $options));
    }
}
