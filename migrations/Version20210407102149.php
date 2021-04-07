<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210407102149 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE organization (id INT IDENTITY NOT NULL, name NVARCHAR(4000) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C1EE637C5E237E06 ON organization (name) WHERE name IS NOT NULL');
        $this->addSql('CREATE TABLE organization_service (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_2C4F129132C8A3DE ON organization_service (organization_id)');
        $this->addSql('CREATE INDEX IDX_2C4F1291ED5CA9E6 ON organization_service (service_id)');
        $this->addSql('CREATE UNIQUE INDEX organization_service_uniqu_idx ON organization_service (organization_id, service_id) WHERE organization_id IS NOT NULL AND service_id IS NOT NULL');
        $this->addSql('CREATE TABLE refresh_tokens (id INT IDENTITY NOT NULL, refresh_token NVARCHAR(128) NOT NULL, username NVARCHAR(255) NOT NULL, valid DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token) WHERE refresh_token IS NOT NULL');
        $this->addSql('CREATE TABLE service (id INT IDENTITY NOT NULL, name NVARCHAR(4000) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD25E237E06 ON service (name) WHERE name IS NOT NULL');
        $this->addSql('CREATE TABLE user_data (id INT IDENTITY NOT NULL, user_id INT NOT NULL, last_name NVARCHAR(50) NOT NULL, first_name NVARCHAR(50) NOT NULL, middle_name NVARCHAR(50), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D772BFAAA76ED395 ON user_data (user_id) WHERE user_id IS NOT NULL');
        $this->addSql('CREATE TABLE usr (id INT IDENTITY NOT NULL, organization_id INT, username NVARCHAR(180) NOT NULL, roles VARCHAR(MAX) NOT NULL, password NVARCHAR(255) NOT NULL, is_active BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1762498CF85E0677 ON usr (username) WHERE username IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_1762498C32C8A3DE ON usr (organization_id)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'usr\', N\'COLUMN\', roles');
        $this->addSql('ALTER TABLE usr ADD CONSTRAINT DF_1762498C_1B5771DD DEFAULT \'1\' FOR is_active');
        $this->addSql('ALTER TABLE organization_service ADD CONSTRAINT FK_2C4F129132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE organization_service ADD CONSTRAINT FK_2C4F1291ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE user_data ADD CONSTRAINT FK_D772BFAAA76ED395 FOREIGN KEY (user_id) REFERENCES usr (id)');
        $this->addSql('ALTER TABLE usr ADD CONSTRAINT FK_1762498C32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE SCHEMA dbo');
        $this->addSql('ALTER TABLE organization_service DROP CONSTRAINT FK_2C4F129132C8A3DE');
        $this->addSql('ALTER TABLE usr DROP CONSTRAINT FK_1762498C32C8A3DE');
        $this->addSql('ALTER TABLE organization_service DROP CONSTRAINT FK_2C4F1291ED5CA9E6');
        $this->addSql('ALTER TABLE user_data DROP CONSTRAINT FK_D772BFAAA76ED395');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE organization_service');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE user_data');
        $this->addSql('DROP TABLE usr');
    }
}
