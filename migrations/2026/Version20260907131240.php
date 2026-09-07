<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260907131240 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        CREATE TABLE `grab_logs` (
            `id` BINARY(16) NOT NULL,
            `created_at` DATETIME(6) DEFAULT NULL,
            `updated_at` DATETIME(6) DEFAULT NULL,
            `counter_company_created` INT DEFAULT 0 NOT NULL,
            `counter_company_updated` INT DEFAULT 0 NOT NULL,
            `counter_vacancy_created` INT DEFAULT 0 NOT NULL,
            `counter_vacancy_updated` INT DEFAULT 0 NOT NULL,
            `description` LONGTEXT DEFAULT NULL,
            `source_id` BINARY(16) DEFAULT NULL,

            INDEX `fk_source_id` (`source_id`),
            INDEX `created_at_idx` (`created_at`),
            INDEX `updated_at_idx` (`updated_at`),
            PRIMARY KEY (`id`)
        ) DEFAULT CHARACTER SET `utf8mb4`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            ADD CONSTRAINT `FK_330BC674953C1C61`
                FOREIGN KEY (`source_id`)
                REFERENCES `sources` (`id`)
                ON DELETE CASCADE
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            DROP FOREIGN KEY `FK_330BC674953C1C61`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP TABLE `grab_logs`
        '
        );
    }
}
