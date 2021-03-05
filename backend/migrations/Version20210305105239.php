<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20210305105239 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id UUID NOT NULL, name VARCHAR(1000) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN author.id IS \'(DC2Type:author_id)\'');
        $this->addSql('CREATE TABLE book (id UUID NOT NULL, owner_id UUID NOT NULL, title VARCHAR(1000) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CBE5A3317E3C61F9 ON book (owner_id)');
        $this->addSql('COMMENT ON COLUMN book.id IS \'(DC2Type:book_id)\'');
        $this->addSql('COMMENT ON COLUMN book.owner_id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE book_author (id SERIAL NOT NULL, book_id UUID DEFAULT NULL, author_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9478D34516A2B381 ON book_author (book_id)');
        $this->addSql('CREATE INDEX IDX_9478D345F675F31B ON book_author (author_id)');
        $this->addSql('COMMENT ON COLUMN book_author.book_id IS \'(DC2Type:book_id)\'');
        $this->addSql('COMMENT ON COLUMN book_author.author_id IS \'(DC2Type:author_id)\'');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3317E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D34516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D345F675F31B FOREIGN KEY (author_id) REFERENCES author (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book_author DROP CONSTRAINT FK_9478D345F675F31B');
        $this->addSql('ALTER TABLE book_author DROP CONSTRAINT FK_9478D34516A2B381');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_author');
    }
}
