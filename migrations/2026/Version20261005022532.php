<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261005022532 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            ADD `user_id` BINARY(16) DEFAULT NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            ADD CONSTRAINT `FK_330BC674A76ED395`
                FOREIGN KEY (`user_id`)
                REFERENCES `users` (`id`)
                ON DELETE SET NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `fk_user_id`
            ON `grab_logs` (`user_id`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            RENAME INDEX `idx_11ba68ca76ed395`
                TO `fk_user_id`
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            DROP FOREIGN KEY `FK_330BC674A76ED395`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `fk_user_id` ON `grab_logs`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            DROP `user_id`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            RENAME INDEX `fk_user_id`
                TO `IDX_11BA68CA76ED395`
        '
        );
    }
}
