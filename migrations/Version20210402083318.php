<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210402083318 extends AbstractMigration
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
        $this->addSql('CREATE TABLE organization_rest_day (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, day DATE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_B5AA3E7232C8A3DE ON organization_rest_day (organization_id)');
        $this->addSql('CREATE TABLE queue (id INT IDENTITY NOT NULL, service_id INT NOT NULL, organization_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_7FFD7F63ED5CA9E6 ON queue (service_id)');
        $this->addSql('CREATE INDEX IDX_7FFD7F6332C8A3DE ON queue (organization_id)');
        $this->addSql('CREATE TABLE queue_day (id INT IDENTITY NOT NULL, queue_id INT NOT NULL, appointment_from TIME(0), appointment_till TIME(0), breakfast_from TIME(0), breakfast_till TIME(0), duration INT, rest INT, persons INT, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_BF5DB4B7477B5BAE ON queue_day (queue_id)');
        $this->addSql('CREATE TABLE queue_default (id INT IDENTITY NOT NULL, queue_id INT NOT NULL, days VARCHAR(MAX) NOT NULL, appointment_from TIME(0) NOT NULL, appointment_till TIME(0) NOT NULL, breakfast_from TIME(0), breakfast_till TIME(0), duration INT NOT NULL, rest INT, persons INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1DC9BE1477B5BAE ON queue_default (queue_id) WHERE queue_id IS NOT NULL');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'queue_default\', N\'COLUMN\', days');
        $this->addSql('CREATE TABLE queue_rest_day (id INT IDENTITY NOT NULL, queue_id INT NOT NULL, day DATE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_9B3163E0477B5BAE ON queue_rest_day (queue_id)');
        $this->addSql('CREATE TABLE refresh_tokens (id INT IDENTITY NOT NULL, refresh_token NVARCHAR(128) NOT NULL, username NVARCHAR(255) NOT NULL, valid DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token) WHERE refresh_token IS NOT NULL');
        $this->addSql('CREATE TABLE service (id INT IDENTITY NOT NULL, name NVARCHAR(4000) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD25E237E06 ON service (name) WHERE name IS NOT NULL');
        $this->addSql('CREATE TABLE usr (id INT IDENTITY NOT NULL, organization_id INT, username NVARCHAR(180) NOT NULL, roles VARCHAR(MAX) NOT NULL, password NVARCHAR(255) NOT NULL, is_active BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1762498CF85E0677 ON usr (username) WHERE username IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_1762498C32C8A3DE ON usr (organization_id)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'usr\', N\'COLUMN\', roles');
        $this->addSql('ALTER TABLE usr ADD CONSTRAINT DF_1762498C_1B5771DD DEFAULT \'1\' FOR is_active');
        $this->addSql('ALTER TABLE organization_rest_day ADD CONSTRAINT FK_B5AA3E7232C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE queue ADD CONSTRAINT FK_7FFD7F63ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE queue ADD CONSTRAINT FK_7FFD7F6332C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE queue_day ADD CONSTRAINT FK_BF5DB4B7477B5BAE FOREIGN KEY (queue_id) REFERENCES queue (id)');
        $this->addSql('ALTER TABLE queue_default ADD CONSTRAINT FK_1DC9BE1477B5BAE FOREIGN KEY (queue_id) REFERENCES queue (id)');
        $this->addSql('ALTER TABLE queue_rest_day ADD CONSTRAINT FK_9B3163E0477B5BAE FOREIGN KEY (queue_id) REFERENCES queue (id)');
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
        $this->addSql('ALTER TABLE organization_rest_day DROP CONSTRAINT FK_B5AA3E7232C8A3DE');
        $this->addSql('ALTER TABLE queue DROP CONSTRAINT FK_7FFD7F6332C8A3DE');
        $this->addSql('ALTER TABLE usr DROP CONSTRAINT FK_1762498C32C8A3DE');
        $this->addSql('ALTER TABLE queue_day DROP CONSTRAINT FK_BF5DB4B7477B5BAE');
        $this->addSql('ALTER TABLE queue_default DROP CONSTRAINT FK_1DC9BE1477B5BAE');
        $this->addSql('ALTER TABLE queue_rest_day DROP CONSTRAINT FK_9B3163E0477B5BAE');
        $this->addSql('ALTER TABLE queue DROP CONSTRAINT FK_7FFD7F63ED5CA9E6');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE organization_rest_day');
        $this->addSql('DROP TABLE queue');
        $this->addSql('DROP TABLE queue_day');
        $this->addSql('DROP TABLE queue_default');
        $this->addSql('DROP TABLE queue_rest_day');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE usr');
    }
}
