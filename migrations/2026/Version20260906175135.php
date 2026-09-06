<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20260906175135 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        INSERT INTO `sources` SET
            `id` = :id,
            `created_at`= NOW(),
            `updated_at`= NOW(),
            `grabber` = :grabber,
            `name` = :name,
            `url` = :url
            ', [
            'id' => Uuid::v7()->toBinary(),
            'grabber' => 'dou',
            'name' => 'DOU',
            'url' => 'https://jobs.dou.ua/vacancies/?remote&category=PHP',
        ]);
    }

    public function down(Schema $schema): void
    {
    }
}
