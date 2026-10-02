<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261002153650 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            ADD `user_id` BINARY(16) DEFAULT NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            ADD CONSTRAINT `FK_99165A59A76ED395`
                FOREIGN KEY (`user_id`)
                REFERENCES `users` (`id`)
                ON DELETE SET NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `IDX_99165A59A76ED395`
            ON `vacancies` (`user_id`)
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            DROP FOREIGN KEY `FK_99165A59A76ED395`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `IDX_99165A59A76ED395`
            ON `vacancies`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            DROP `user_id`
        '
        );
    }
}
