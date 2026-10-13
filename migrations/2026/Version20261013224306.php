<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261013224306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_name` ON `companies` (`name`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_created_at` ON `companies` (`created_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_updated_at` ON `companies` (`updated_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE UNIQUE INDEX `uniq_id` ON `companies` (`id`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_created_at` ON `grab_logs` (`created_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_updated_at` ON `grab_logs` (`updated_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE UNIQUE INDEX `uniq_id` ON `grab_logs` (`id`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_created_at` ON `notes` (`created_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_updated_at` ON `notes` (`updated_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE UNIQUE INDEX `uniq_id` ON `notes` (`id`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_created_at` ON `sources` (`created_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_updated_at` ON `sources` (`updated_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE UNIQUE INDEX `uniq_id` ON `sources` (`id`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_created_at` ON `users` (`created_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_updated_at` ON `users` (`updated_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE UNIQUE INDEX `uniq_id` ON `users` (`id`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_created_at` ON `vacancies` (`created_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE INDEX `idx_updated_at` ON `vacancies` (`updated_at`)
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        CREATE UNIQUE INDEX `uniq_id` ON `vacancies` (`id`)
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_name` ON `companies`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_created_at` ON `companies`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_updated_at` ON `companies`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `uniq_id` ON `companies`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_created_at` ON `grab_logs`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_updated_at` ON `grab_logs`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `uniq_id` ON `grab_logs`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_created_at` ON `notes`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_updated_at` ON `notes`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `uniq_id` ON `notes`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_created_at` ON `sources`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_updated_at` ON `sources`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `uniq_id` ON `sources`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_created_at` ON `users`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_updated_at` ON `users`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `uniq_id` ON `users`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_created_at` ON `vacancies`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `idx_updated_at` ON `vacancies`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        DROP INDEX `uniq_id` ON `vacancies`
        '
        );
    }
}
