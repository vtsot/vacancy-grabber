<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261001231637 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        CREATE TABLE `users` (
            `email` VARCHAR(255) NOT NULL,
            `password` VARCHAR(255) DEFAULT NULL,
            `roles` JSON NOT NULL,
            `id` BINARY(16) NOT NULL,
            `created_at` DATETIME(6) DEFAULT NULL,
            `updated_at` DATETIME(6) DEFAULT NULL,
            `active` TINYINT DEFAULT 1 NOT NULL,

            INDEX `idx_active` (`active`),
            UNIQUE INDEX `uniq_email` (`email`),
            PRIMARY KEY (`id`)
        ) DEFAULT CHARACTER SET `utf8mb4`
        '
        );

        $this->addSql(/** @lang MySQL */ 'DROP INDEX `created_at_idx` ON `companies`');

        $this->addSql(/** @lang MySQL */ 'DROP INDEX `updated_at_idx` ON `companies`');

        $this->addSql(/** @lang MySQL */ 'DROP INDEX `created_at_idx` ON `grab_logs`');

        $this->addSql(/** @lang MySQL */ 'DROP INDEX `updated_at_idx` ON `grab_logs`');

        $this->addSql(/** @lang MySQL */ 'DROP INDEX `updated_at_idx` ON `notes`');

        $this->addSql(/** @lang MySQL */ 'DROP INDEX `created_at_idx` ON `notes`');

        $this->addSql(/** @lang MySQL */ 'DROP INDEX `updated_at_idx` ON `sources`');

        $this->addSql(/** @lang MySQL */ 'DROP INDEX `created_at_idx` ON `sources`');

        $this->addSql(/** @lang MySQL */ 'DROP INDEX `created_at_idx` ON `vacancies`');

        $this->addSql(/** @lang MySQL */ 'DROP INDEX `updated_at_idx` ON `vacancies`');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */ 'DROP TABLE `users`');

        $this->addSql(/** @lang MySQL */ 'CREATE INDEX `created_at_idx` ON `companies` (created_at)');

        $this->addSql(/** @lang MySQL */ 'CREATE INDEX `updated_at_idx` ON `companies` (`updated_at`)');

        $this->addSql(/** @lang MySQL */ 'CREATE INDEX `created_at_idx` ON `grab_logs` (`created_at`)');

        $this->addSql(/** @lang MySQL */ 'CREATE INDEX `updated_at_idx` ON `grab_logs` (`updated_at`)');

        $this->addSql(/** @lang MySQL */ 'CREATE INDEX `updated_at_idx` ON `notes` (`updated_at`)');

        $this->addSql(/** @lang MySQL */ 'CREATE INDEX `created_at_idx` ON `notes` (`created_at`)');

        $this->addSql(/** @lang MySQL */ 'CREATE INDEX `updated_at_idx` ON `sources` (`updated_at`)');

        $this->addSql(/** @lang MySQL */ 'CREATE INDEX `created_at_idx` ON `sources` (`created_at`)');

        $this->addSql(/** @lang MySQL */ 'CREATE INDEX `created_at_idx` ON `vacancies` (`created_at`)');

        $this->addSql(/** @lang MySQL */ 'CREATE INDEX `updated_at_idx` ON `vacancies` (`updated_at`)');
    }
}
