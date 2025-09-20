<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250920122949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album ADD COLUMN description CLOB DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__album AS SELECT id, title, critic_score, user_score, release_date, genres, photo_url FROM album');
        $this->addSql('DROP TABLE album');
        $this->addSql('CREATE TABLE album (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, critic_score INTEGER NOT NULL, user_score INTEGER DEFAULT 0 NOT NULL, release_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , genres CLOB NOT NULL --(DC2Type:json)
        , photo_url VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO album (id, title, critic_score, user_score, release_date, genres, photo_url) SELECT id, title, critic_score, user_score, release_date, genres, photo_url FROM __temp__album');
        $this->addSql('DROP TABLE __temp__album');
    }
}
