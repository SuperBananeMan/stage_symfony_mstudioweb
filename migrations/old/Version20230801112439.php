<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230801112439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F8174C36989D9B62 ON adminforum (slug)');
        $this->addSql('DROP INDEX slug_page ON adminforumpage');
        $this->addSql('DROP INDEX username ON adminforumpage');
        $this->addSql('ALTER TABLE adminforumpage CHANGE content content TEXT NOT NULL');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY comments_ibfk_1');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY comments_ibfk_2');
        $this->addSql('DROP INDEX comments_ibfk_1 ON comments');
        $this->addSql('DROP INDEX video ON comments');
        $this->addSql('ALTER TABLE user DROP INDEX username, ADD UNIQUE INDEX UNIQ_8D93D649F85E0677 (username)');
        $this->addSql('ALTER TABLE user DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY uploader_id1');
        $this->addSql('DROP INDEX uploader_id1 ON videos');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F8174C36989D9B62 ON adminforum');
        $this->addSql('ALTER TABLE adminforumpage CHANGE content content LONGTEXT NOT NULL');
        $this->addSql('CREATE INDEX slug_page ON adminforumpage (slugpage(250))');
        $this->addSql('CREATE INDEX username ON adminforumpage (username(250))');
        $this->addSql('ALTER TABLE user DROP INDEX UNIQ_8D93D649F85E0677, ADD INDEX username (username)');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT comments_ibfk_1 FOREIGN KEY (uploader) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT comments_ibfk_2 FOREIGN KEY (video) REFERENCES videos (id)');
        $this->addSql('CREATE INDEX comments_ibfk_1 ON comments (uploader)');
        $this->addSql('CREATE INDEX video ON comments (video)');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT uploader_id1 FOREIGN KEY (uploader) REFERENCES user (id)');
        $this->addSql('CREATE INDEX uploader_id1 ON videos (uploader)');
    }
}
