<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250919201259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE song');
        $this->addSql('DROP TABLE song_artist');
        $this->addSql('ALTER TABLE album ADD COLUMN genres CLOB NOT NULL DEFAULT \'[]\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE song (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE "BINARY", user_score INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE song_artist (song_id INTEGER NOT NULL, artist_id INTEGER NOT NULL, PRIMARY KEY(song_id, artist_id), CONSTRAINT FK_722870DA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_722870DB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_722870DB7970CF8 ON song_artist (artist_id)');
        $this->addSql('CREATE INDEX IDX_722870DA0BDB2F3 ON song_artist (song_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__album AS SELECT id, title, critic_score, user_score, release_date, photo_url FROM album');
        $this->addSql('DROP TABLE album');
        $this->addSql('CREATE TABLE album (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, critic_score INTEGER NOT NULL, user_score INTEGER DEFAULT 0 NOT NULL, release_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , photo_url VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO album (id, title, critic_score, user_score, release_date, photo_url) SELECT id, title, critic_score, user_score, release_date, photo_url FROM __temp__album');
        $this->addSql('DROP TABLE __temp__album');
    }
}
