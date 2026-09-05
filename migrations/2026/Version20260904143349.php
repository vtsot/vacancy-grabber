<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260904143349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        CREATE TABLE `companies` (
            `id` BINARY(16) NOT NULL,
            `name` VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARACTER SET utf8mb4
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE TABLE `vacancies` (
            `id` BINARY(16) NOT NULL,
            `name` VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARACTER SET utf8mb4
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */ 'DROP TABLE `companies`');

        $this->addSql(/** @lang MySQL */ 'DROP TABLE `vacancies`');
    }
}
