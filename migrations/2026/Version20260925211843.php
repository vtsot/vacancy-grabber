<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260925211843 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        UPDATE `vacancies` SET `status` = \'done\' WHERE `status` = \'Done\'
        '
        );
    }

    public function down(Schema $schema): void
    {
    }
}
