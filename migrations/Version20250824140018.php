<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250824140018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__album AS SELECT id, title, criric_score, user_score, release_date FROM album');
        $this->addSql('DROP TABLE album');
        $this->addSql('CREATE TABLE album (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, critic_score INTEGER NOT NULL, user_score INTEGER NOT NULL, release_date DATETIME NOT NULL, photo_url VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO album (id, title, critic_score, user_score, release_date) SELECT id, title, criric_score, user_score, release_date FROM __temp__album');
        $this->addSql('DROP TABLE __temp__album');
        $this->addSql('ALTER TABLE artist ADD COLUMN photo_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD COLUMN created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE review ADD COLUMN updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user ADD COLUMN photo_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__album AS SELECT id, title, critic_score, user_score, release_date FROM album');
        $this->addSql('DROP TABLE album');
        $this->addSql('CREATE TABLE album (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, criric_score INTEGER NOT NULL, user_score INTEGER NOT NULL, release_date DATETIME NOT NULL)');
        $this->addSql('INSERT INTO album (id, title, criric_score, user_score, release_date) SELECT id, title, critic_score, user_score, release_date FROM __temp__album');
        $this->addSql('DROP TABLE __temp__album');
        $this->addSql('CREATE TEMPORARY TABLE __temp__artist AS SELECT id, name, bio FROM artist');
        $this->addSql('DROP TABLE artist');
        $this->addSql('CREATE TABLE artist (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, bio CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO artist (id, name, bio) SELECT id, name, bio FROM __temp__artist');
        $this->addSql('DROP TABLE __temp__artist');
        $this->addSql('CREATE TEMPORARY TABLE __temp__review AS SELECT id, author_id, album_id, text, score FROM review');
        $this->addSql('DROP TABLE review');
        $this->addSql('CREATE TABLE review (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, album_id INTEGER NOT NULL, text CLOB DEFAULT NULL, score INTEGER DEFAULT NULL, CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_794381C61137ABCF FOREIGN KEY (album_id) REFERENCES album (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO review (id, author_id, album_id, text, score) SELECT id, author_id, album_id, text, score FROM __temp__review');
        $this->addSql('DROP TABLE __temp__review');
        $this->addSql('CREATE INDEX IDX_794381C6F675F31B ON review (author_id)');
        $this->addSql('CREATE INDEX IDX_794381C61137ABCF ON review (album_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, email, roles, password) SELECT id, email, roles, password FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }
}
