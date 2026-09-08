<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260908141511 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `sources`
            ADD `active` TINYINT DEFAULT 1 NOT NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_active`
            ON `sources` (`active`)
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_active`
            ON `sources`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `sources`
            DROP `active`
        '
        );
    }
}
