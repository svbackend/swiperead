<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210302083301 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE api_token (id SERIAL NOT NULL, user_id UUID DEFAULT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BA2F5EB5F37A13B ON api_token (token)');
        $this->addSql('CREATE INDEX IDX_7BA2F5EBA76ED395 ON api_token (user_id)');
        $this->addSql('COMMENT ON COLUMN api_token.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_confirmed BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE users_networks (id SERIAL NOT NULL, user_id UUID NOT NULL, network VARCHAR(255) NOT NULL, network_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A2F89FF1A76ED395 ON users_networks (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2F89FF1608487BC34128B91 ON users_networks (network, network_id)');
        $this->addSql('COMMENT ON COLUMN users_networks.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE users_roles (id SERIAL NOT NULL, user_id UUID NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_51498A8EA76ED395 ON users_roles (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_51498A8E57698A6AA76ED395 ON users_roles (role, user_id)');
        $this->addSql('COMMENT ON COLUMN users_roles.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_networks ADD CONSTRAINT FK_A2F89FF1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE api_token DROP CONSTRAINT FK_7BA2F5EBA76ED395');
        $this->addSql('ALTER TABLE users_networks DROP CONSTRAINT FK_A2F89FF1A76ED395');
        $this->addSql('ALTER TABLE users_roles DROP CONSTRAINT FK_51498A8EA76ED395');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_networks');
        $this->addSql('DROP TABLE users_roles');
    }
}
