<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261017120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add the counter of vacancies updated by a grab to grab logs';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs` ADD `counter_vacancy_updated` INT DEFAULT 0 NOT NULL AFTER `counter_vacancy_created`
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs` DROP `counter_vacancy_updated`
        '
        );
    }
}
