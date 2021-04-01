<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210329133722 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE organization (id INT IDENTITY NOT NULL, name NVARCHAR(4000) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX name_idx ON organization (name)');
        $this->addSql('CREATE TABLE refresh_tokens (id INT IDENTITY NOT NULL, refresh_token NVARCHAR(128) NOT NULL, username NVARCHAR(255) NOT NULL, valid DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token) WHERE refresh_token IS NOT NULL');
        $this->addSql('CREATE TABLE service (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, name NVARCHAR(255) NOT NULL, days VARCHAR(MAX) NOT NULL, reception_time_from TIME(0) NOT NULL, reception_time_till TIME(0) NOT NULL, rest_time_from TIME(0), rest_time_till TIME(0), duration SMALLINT NOT NULL, rest SMALLINT, persons SMALLINT NOT NULL, is_active BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_E19D9AD232C8A3DE ON service (organization_id)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'service\', N\'COLUMN\', days');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT DF_E19D9AD2_1B5771DD DEFAULT \'1\' FOR is_active');
        $this->addSql('CREATE TABLE user_data (id INT IDENTITY NOT NULL, last_name NVARCHAR(50) NOT NULL, first_name NVARCHAR(50) NOT NULL, middle_name NVARCHAR(50), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE usr (id INT IDENTITY NOT NULL, user_data_id INT, organization_id INT, username NVARCHAR(180) NOT NULL, roles VARCHAR(MAX) NOT NULL, password NVARCHAR(255) NOT NULL, is_active BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1762498CF85E0677 ON usr (username) WHERE username IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1762498C6FF8BF36 ON usr (user_data_id) WHERE user_data_id IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_1762498C32C8A3DE ON usr (organization_id)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'usr\', N\'COLUMN\', roles');
        $this->addSql('ALTER TABLE usr ADD CONSTRAINT DF_1762498C_1B5771DD DEFAULT \'1\' FOR is_active');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD232C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE usr ADD CONSTRAINT FK_1762498C6FF8BF36 FOREIGN KEY (user_data_id) REFERENCES user_data (id)');
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
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD232C8A3DE');
        $this->addSql('ALTER TABLE usr DROP CONSTRAINT FK_1762498C32C8A3DE');
        $this->addSql('ALTER TABLE usr DROP CONSTRAINT FK_1762498C6FF8BF36');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE user_data');
        $this->addSql('DROP TABLE usr');
    }
}
