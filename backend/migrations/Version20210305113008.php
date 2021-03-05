<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210305113008 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE book_chapter (id SERIAL NOT NULL, book_id UUID NOT NULL, name VARCHAR(1000) NOT NULL, ordering INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6AA19DB816A2B381 ON book_chapter (book_id)');
        $this->addSql('COMMENT ON COLUMN book_chapter.book_id IS \'(DC2Type:book_id)\'');
        $this->addSql('CREATE TABLE book_chapter_card (id SERIAL NOT NULL, chapter_id INT NOT NULL, content TEXT NOT NULL, ordering INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6304261E579F4768 ON book_chapter_card (chapter_id)');
        $this->addSql('ALTER TABLE book_chapter ADD CONSTRAINT FK_6AA19DB816A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_chapter_card ADD CONSTRAINT FK_6304261E579F4768 FOREIGN KEY (chapter_id) REFERENCES book_chapter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE book_chapter_card DROP CONSTRAINT FK_6304261E579F4768');
        $this->addSql('DROP TABLE book_chapter');
        $this->addSql('DROP TABLE book_chapter_card');
    }
}
