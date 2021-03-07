<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210307102643 extends AbstractMigration
{

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE bookmark (id SERIAL NOT NULL, book_id UUID NOT NULL, card_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA62921D16A2B381 ON bookmark (book_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA62921D4ACC9A20 ON bookmark (card_id)');
        $this->addSql('COMMENT ON COLUMN bookmark.book_id IS \'(DC2Type:book_id)\'');
        $this->addSql('ALTER TABLE bookmark ADD CONSTRAINT FK_DA62921D16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bookmark ADD CONSTRAINT FK_DA62921D4ACC9A20 FOREIGN KEY (card_id) REFERENCES book_chapter_card (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE bookmark');
    }
}
