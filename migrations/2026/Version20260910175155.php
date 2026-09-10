<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260910175155 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            ADD `status` VARCHAR(255) DEFAULT \'feedback\' NOT NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_status`
            ON `vacancies` (`status`)
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_status`
            ON `vacancies`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            DROP `status`
        '
        );
    }
}
