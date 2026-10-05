<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261004235302 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            ADD `user_id` BINARY(16) DEFAULT NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            ADD CONSTRAINT `FK_11BA68CA76ED395`
                FOREIGN KEY (`user_id`)
                REFERENCES `users` (`id`)
                ON DELETE SET NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `IDX_11BA68CA76ED395`
            ON `notes` (`user_id`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            RENAME INDEX `idx_99165a59a76ed395` TO `fk_user_id`
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            DROP FOREIGN KEY `FK_11BA68CA76ED395`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `IDX_11BA68CA76ED395` ON `notes`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            DROP `user_id`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            RENAME INDEX `fk_user_id` TO `IDX_99165A59A76ED395`
        '
        );
    }
}
