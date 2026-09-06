<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260906173232 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */'
        CREATE TABLE `companies` (
            `id` BINARY(16) NOT NULL,
            `created_at` DATETIME(6) DEFAULT NULL,
            `updated_at` DATETIME(6) DEFAULT NULL,
            `name` VARCHAR(255) DEFAULT NULL,
            `url` VARCHAR(255) DEFAULT NULL,

            INDEX `created_at_idx` (`created_at`),
            INDEX `updated_at_idx` (`updated_at`),
            PRIMARY KEY (`id`)
        ) DEFAULT CHARACTER SET `utf8mb4`
        ');

        $this->addSql(/** @lang MySQL */'
        CREATE TABLE `notes` (
            `id` BINARY(16) NOT NULL,
            `created_at` DATETIME(6) DEFAULT NULL,
            `updated_at` DATETIME(6) DEFAULT NULL,
            `noted_at` DATETIME(6) DEFAULT NULL,
            `description` LONGTEXT DEFAULT NULL,
            `company_id` BINARY(16) DEFAULT NULL,
            `vacancy_id` BINARY(16) DEFAULT NULL,

            INDEX `fk_company_id` (`company_id`),
            INDEX `fk_vacancy_id` (`vacancy_id`),
            INDEX `idx_noted_at` (`noted_at`),
            INDEX `created_at_idx` (`created_at`),
            INDEX `updated_at_idx` (`updated_at`),
            PRIMARY KEY (`id`)
        ) DEFAULT CHARACTER SET `utf8mb4`
        ');

        $this->addSql(/** @lang MySQL */'
        CREATE TABLE `sources` (
            `id` BINARY(16) NOT NULL,
            `created_at` DATETIME(6) DEFAULT NULL,
            `updated_at` DATETIME(6) DEFAULT NULL,
            `grabber` VARCHAR(255) DEFAULT \'dou\' NOT NULL,
            `name` VARCHAR(255) DEFAULT NULL,
            `url` VARCHAR(255) DEFAULT NULL,

            INDEX `created_at_idx` (`created_at`),
            INDEX `updated_at_idx` (`updated_at`),
            PRIMARY KEY (`id`)
        ) DEFAULT CHARACTER SET `utf8mb4`
        ');

        $this->addSql(/** @lang MySQL */'
        CREATE TABLE `vacancies` (
            `id` BINARY(16) NOT NULL,
            `created_at` DATETIME(6) DEFAULT NULL,
            `updated_at` DATETIME(6) DEFAULT NULL,
            `resume_sent_at` DATETIME(6) DEFAULT NULL,
            `title` VARCHAR(255) DEFAULT NULL,
            `description` LONGTEXT DEFAULT NULL,
            `external_id` VARCHAR(255) DEFAULT NULL,
            `url` VARCHAR(255) DEFAULT NULL,
            `salary` VARCHAR(255) DEFAULT NULL,
            `source_id` BINARY(16) DEFAULT NULL,
            `company_id` BINARY(16) DEFAULT NULL,

            INDEX `fk_source_id` (`source_id`),
            INDEX `fk_company_id` (`company_id`),
            INDEX `idx_resume_sent_at` (`resume_sent_at`),
            INDEX `created_at_idx` (`created_at`),
            INDEX `updated_at_idx` (`updated_at`),
            PRIMARY KEY (`id`)
        ) DEFAULT CHARACTER SET `utf8mb4`
        ');

        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `notes`
            ADD CONSTRAINT `FK_11BA68C979B1AD6`
                FOREIGN KEY (`company_id`)
                REFERENCES `companies` (`id`)
                ON DELETE CASCADE
        ');

        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `notes`
            ADD CONSTRAINT `FK_11BA68C433B78C4`
                FOREIGN KEY (`vacancy_id`)
                REFERENCES `vacancies` (`id`)
                ON DELETE CASCADE
        ');

        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `vacancies`
            ADD CONSTRAINT `FK_99165A59953C1C61`
                FOREIGN KEY (`source_id`)
                REFERENCES `sources` (`id`)
                ON DELETE CASCADE
        ');

        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `vacancies`
            ADD CONSTRAINT `FK_99165A59979B1AD6`
                FOREIGN KEY (`company_id`)
                REFERENCES `companies` (`id`)
                ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `notes`
            DROP FOREIGN KEY `FK_11BA68C979B1AD6`
        ');

        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `notes`
            DROP FOREIGN KEY `FK_11BA68C433B78C4`
        ');

        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `vacancies`
            DROP FOREIGN KEY `FK_99165A59953C1C61`
        ');

        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `vacancies`
            DROP FOREIGN KEY `FK_99165A59979B1AD6`
        ');

        $this->addSql(/** @lang MySQL */'
        DROP TABLE `companies`
        ');

        $this->addSql(/** @lang MySQL */'
        DROP TABLE `notes`
        ');

        $this->addSql(/** @lang MySQL */'
        DROP TABLE `sources`
        ');

        $this->addSql(/** @lang MySQL */'
        DROP TABLE `vacancies`
        ');
    }
}
