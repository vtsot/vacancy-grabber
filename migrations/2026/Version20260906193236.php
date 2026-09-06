<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260906193236 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `companies`
            CHANGE `name` `name` VARCHAR(255) NOT NULL
        ');

        $this->addSql(/** @lang MySQL */'
        CREATE UNIQUE INDEX `uniq_name`
            ON `companies` (`name`)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */'
        DROP INDEX `uniq_name`
            ON `companies`
        ');

        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `companies`
            CHANGE `name` `name` VARCHAR(255) DEFAULT NULL
        ');
    }
}
