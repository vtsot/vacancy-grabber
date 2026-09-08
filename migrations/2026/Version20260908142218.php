<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20260908142218 extends AbstractMigration
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
            `url` = :url,
            `active` = :active
            ', [
            'id'      => Uuid::v7()->toBinary(),
            'grabber' => 'djinni',
            'name'    => 'Djinni',
            'url'     => 'https://djinni.co/jobs/?employment=remote&primary_keyword=Symfony',
            'active'  => 0,
        ]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ '
        DELETE FROM `sources` WHERE `grabber` = :grabber
        ', [
            'grabber' => 'djinni',
        ]);
    }
}
