<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261010204503 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            ADD `dead_line_at` DATETIME(6) DEFAULT NULL
                AFTER `resume_sent_at`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_dead_line_at`
            ON `vacancies` (`dead_line_at`)
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_dead_line_at` ON `vacancies`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            DROP `dead_line_at`
        '
        );
    }
}
