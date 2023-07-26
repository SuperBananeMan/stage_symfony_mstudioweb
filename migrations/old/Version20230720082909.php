<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230720082909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE filevideo CHANGE image_name video_name VARCHAR(255) DEFAULT NULL, CHANGE image_size video_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos ADD video_name VARCHAR(255) DEFAULT NULL, ADD video_size INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE filevideo CHANGE video_name image_name VARCHAR(255) DEFAULT NULL, CHANGE video_size image_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos DROP video_name, DROP video_size');
    }
}
