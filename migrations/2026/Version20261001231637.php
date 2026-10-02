<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261001231637 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (email VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME(6) DEFAULT NULL, updated_at DATETIME(6) DEFAULT NULL, active TINYINT DEFAULT 1 NOT NULL, is_google_authenticator_enabled TINYINT DEFAULT 0 NOT NULL, google_authenticator_token VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, INDEX idx_active (active), INDEX idx_google_authenticator_enabled (is_google_authenticator_enabled), UNIQUE INDEX uniq_email (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('DROP INDEX updated_at_idx ON companies');
        $this->addSql('DROP INDEX created_at_idx ON companies');
        $this->addSql('DROP INDEX created_at_idx ON grab_logs');
        $this->addSql('DROP INDEX updated_at_idx ON grab_logs');
        $this->addSql('DROP INDEX updated_at_idx ON notes');
        $this->addSql('DROP INDEX created_at_idx ON notes');
        $this->addSql('DROP INDEX created_at_idx ON sources');
        $this->addSql('DROP INDEX updated_at_idx ON sources');
        $this->addSql('DROP INDEX created_at_idx ON vacancies');
        $this->addSql('DROP INDEX updated_at_idx ON vacancies');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE INDEX updated_at_idx ON companies (updated_at)');
        $this->addSql('CREATE INDEX created_at_idx ON companies (created_at)');
        $this->addSql('CREATE INDEX created_at_idx ON grab_logs (created_at)');
        $this->addSql('CREATE INDEX updated_at_idx ON grab_logs (updated_at)');
        $this->addSql('CREATE INDEX updated_at_idx ON notes (updated_at)');
        $this->addSql('CREATE INDEX created_at_idx ON notes (created_at)');
        $this->addSql('CREATE INDEX created_at_idx ON sources (created_at)');
        $this->addSql('CREATE INDEX updated_at_idx ON sources (updated_at)');
        $this->addSql('CREATE INDEX created_at_idx ON vacancies (created_at)');
        $this->addSql('CREATE INDEX updated_at_idx ON vacancies (updated_at)');
    }
}
