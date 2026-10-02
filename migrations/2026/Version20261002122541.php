<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261002122541 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `sources`
            ADD `user_id` BINARY(16) DEFAULT NULL
        ');

        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `sources`
            ADD CONSTRAINT `FK_D25D65F2A76ED395`
                FOREIGN KEY (`user_id`)
                REFERENCES `users` (`id`)
                ON DELETE SET NULL
        ');

        $this->addSql(/** @lang MySQL */'
        CREATE INDEX `fk_user_id`
            ON `sources` (`user_id`)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `sources`
            DROP FOREIGN KEY `FK_D25D65F2A76ED395`
        ');

        $this->addSql(/** @lang MySQL */'
        DROP INDEX `fk_user_id` ON `sources`
        ');

        $this->addSql(/** @lang MySQL */'
        ALTER TABLE `sources`
            DROP `user_id`
        ');
    }
}
