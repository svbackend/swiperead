<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210307102815 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA62921D16A2B3814ACC9A20 ON bookmark (book_id, card_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP INDEX UNIQ_DA62921D16A2B3814ACC9A20');
    }
}
