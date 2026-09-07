<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260907135929 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            DROP `counter_company_updated`,
            DROP `counter_vacancy_updated`
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            ADD `counter_company_updated` INT DEFAULT 0 NOT NULL,
            ADD `counter_vacancy_updated` INT DEFAULT 0 NOT NULL
        '
        );
    }
}
