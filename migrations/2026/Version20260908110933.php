<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260908110933 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            DROP FOREIGN KEY `FK_330BC674953C1C61`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            ADD CONSTRAINT `FK_330BC674953C1C61`
                FOREIGN KEY (`source_id`)
                REFERENCES `sources` (`id`)
                ON DELETE SET NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            DROP FOREIGN KEY `FK_11BA68C433B78C4`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            DROP FOREIGN KEY `FK_11BA68C979B1AD6`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            ADD CONSTRAINT `FK_11BA68C433B78C4`
                FOREIGN KEY (`vacancy_id`)
                REFERENCES `vacancies` (`id`)
                ON DELETE SET NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            ADD CONSTRAINT `FK_11BA68C979B1AD6`
                FOREIGN KEY (`company_id`)
                REFERENCES `companies` (`id`)
                ON DELETE SET NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            DROP FOREIGN KEY `FK_99165A59953C1C61`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            DROP FOREIGN KEY `FK_99165A59979B1AD6`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            ADD CONSTRAINT `FK_99165A59953C1C61`
                FOREIGN KEY (`source_id`)
                REFERENCES `sources` (`id`)
                ON DELETE SET NULL
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            ADD CONSTRAINT `FK_99165A59979B1AD6`
                FOREIGN KEY (`company_id`)
                REFERENCES `companies` (`id`)
                ON DELETE SET NULL
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            DROP FOREIGN KEY `FK_330BC674953C1C61`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `grab_logs`
            ADD CONSTRAINT `FK_330BC674953C1C61`
                FOREIGN KEY (`source_id`)
                REFERENCES `sources` (`id`)
                ON UPDATE NO ACTION
                ON DELETE CASCADE
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            DROP FOREIGN KEY `FK_11BA68C979B1AD6`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            DROP FOREIGN KEY `FK_11BA68C433B78C4`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            ADD CONSTRAINT `FK_11BA68C979B1AD6`
                FOREIGN KEY (`company_id`)
                REFERENCES `companies` (`id`)
                ON UPDATE NO ACTION
                ON DELETE CASCADE
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `notes`
            ADD CONSTRAINT `FK_11BA68C433B78C4`
                FOREIGN KEY (`vacancy_id`)
                REFERENCES `vacancies` (`id`)
                ON UPDATE NO ACTION
                ON DELETE CASCADE
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            DROP FOREIGN KEY `FK_99165A59953C1C61`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            DROP FOREIGN KEY `FK_99165A59979B1AD6`
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            ADD CONSTRAINT `FK_99165A59953C1C61`
                FOREIGN KEY (`source_id`)
                REFERENCES `sources` (`id`)
                ON UPDATE NO ACTION
                ON DELETE CASCADE
        '
        );

        $this->addSql(
        /** @lang MySQL */ '
        ALTER TABLE `vacancies`
            ADD CONSTRAINT `FK_99165A59979B1AD6`
                FOREIGN KEY (`company_id`)
                REFERENCES `companies` (`id`)
                ON UPDATE NO ACTION
                ON DELETE CASCADE
        '
        );
    }
}
