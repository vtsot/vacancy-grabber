<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260911205603 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            CHANGE `status` `status` VARCHAR(255) DEFAULT \'pending\' NOT NULL
        '
        );

        $this->addSql(/** @lang MySQL */'
        UPDATE `vacancies` SET
            `status` = \'pending\'
        WHERE `status` = \'feedback\'
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            CHANGE `status` `status` VARCHAR(255) DEFAULT \'feedback\' NOT NULL
        '
        );
    }
}
