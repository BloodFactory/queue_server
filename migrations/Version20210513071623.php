<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210513071623 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA log');
        $this->addSql('CREATE TABLE appointment (id INT IDENTITY NOT NULL, organization_service_id INT NOT NULL, date DATE NOT NULL, time_from TIME(0) NOT NULL, time_till TIME(0) NOT NULL, need_dinner BIT NOT NULL, dinner_from TIME(0), dinner_till TIME(0), duration INT NOT NULL, persons INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_FE38F8444073B09 ON appointment (organization_service_id)');
        $this->addSql('CREATE UNIQUE INDEX organization_service_date_unique_idx ON appointment (organization_service_id, date) WHERE organization_service_id IS NOT NULL AND date IS NOT NULL');
        $this->addSql('CREATE TABLE department (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, name NVARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_CD1DE18A32C8A3DE ON department (organization_id)');
        $this->addSql('CREATE TABLE log.error_log (id UNIQUEIDENTIFIER NOT NULL, usr_id INT, message VARCHAR(MAX) NOT NULL, code INT NOT NULL, moment DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX usr_idx ON log.error_log (usr_id)');
        $this->addSql('CREATE TABLE organization (id INT IDENTITY NOT NULL, parent_id INT, name NVARCHAR(4000) NOT NULL, timezone SMALLINT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C1EE637C5E237E06 ON organization (name) WHERE name IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_C1EE637C727ACA70 ON organization (parent_id)');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT DF_C1EE637C_3701B297 DEFAULT 3 FOR timezone');
        $this->addSql('CREATE TABLE organization_service (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_2C4F129132C8A3DE ON organization_service (organization_id)');
        $this->addSql('CREATE INDEX IDX_2C4F1291ED5CA9E6 ON organization_service (service_id)');
        $this->addSql('CREATE UNIQUE INDEX organization_service_unique_idx ON organization_service (organization_id, service_id) WHERE organization_id IS NOT NULL AND service_id IS NOT NULL');
        $this->addSql('CREATE TABLE refresh_tokens (id INT IDENTITY NOT NULL, refresh_token NVARCHAR(128) NOT NULL, username NVARCHAR(255) NOT NULL, valid DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token) WHERE refresh_token IS NOT NULL');
        $this->addSql('CREATE TABLE registration (id INT IDENTITY NOT NULL, appointment_id INT NOT NULL, time TIME(0) NOT NULL, last_name NVARCHAR(50) NOT NULL, first_name NVARCHAR(50) NOT NULL, middle_name NVARCHAR(50), birthday DATE NOT NULL, phone NVARCHAR(21), email NVARCHAR(255), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_62A8A7A7E5B533F9 ON registration (appointment_id)');
        $this->addSql('CREATE INDEX person_idx ON registration (birthday, last_name, first_name, middle_name)');
        $this->addSql('CREATE TABLE log.request_log (id UNIQUEIDENTIFIER NOT NULL, usr_id INT, moment DATETIME2(6) NOT NULL, content VARCHAR(MAX), method NVARCHAR(15) NOT NULL, path NVARCHAR(255) NOT NULL, query VARCHAR(MAX), request VARCHAR(MAX), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_A14398BAC69D3FB ON log.request_log (usr_id)');
        $this->addSql('CREATE INDEX user_path_idx ON log.request_log (usr_id, path)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'log\', N\'TABLE\', \'request_log\', N\'COLUMN\', content');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'log\', N\'TABLE\', \'request_log\', N\'COLUMN\', query');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'log\', N\'TABLE\', \'request_log\', N\'COLUMN\', request');
        $this->addSql('CREATE TABLE service (id INT IDENTITY NOT NULL, service_group_id INT NOT NULL, name NVARCHAR(4000) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_E19D9AD2722827A ON service (service_group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD2722827A5E237E06 ON service (service_group_id, name) WHERE service_group_id IS NOT NULL AND name IS NOT NULL');
        $this->addSql('CREATE TABLE services_group (id INT IDENTITY NOT NULL, name NVARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D122FE875E237E06 ON services_group (name) WHERE name IS NOT NULL');
        $this->addSql('CREATE TABLE user_data (id INT IDENTITY NOT NULL, user_id INT NOT NULL, last_name NVARCHAR(50) NOT NULL, first_name NVARCHAR(50) NOT NULL, middle_name NVARCHAR(50), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D772BFAAA76ED395 ON user_data (user_id) WHERE user_id IS NOT NULL');
        $this->addSql('CREATE TABLE user_settings (id INT IDENTITY NOT NULL, usr_id INT NOT NULL, dark_mode BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C844C5C69D3FB ON user_settings (usr_id) WHERE usr_id IS NOT NULL');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT DF_5C844C5_EECE9A15 DEFAULT \'0\' FOR dark_mode');
        $this->addSql('CREATE TABLE usr (id INT IDENTITY NOT NULL, organization_id INT, username NVARCHAR(180) NOT NULL, roles VARCHAR(MAX) NOT NULL, password NVARCHAR(255) NOT NULL, is_active BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1762498CF85E0677 ON usr (username) WHERE username IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_1762498C32C8A3DE ON usr (organization_id)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'usr\', N\'COLUMN\', roles');
        $this->addSql('ALTER TABLE usr ADD CONSTRAINT DF_1762498C_1B5771DD DEFAULT \'1\' FOR is_active');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8444073B09 FOREIGN KEY (organization_service_id) REFERENCES organization_service (id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE log.error_log ADD CONSTRAINT FK_F3565581C69D3FB FOREIGN KEY (usr_id) REFERENCES usr (id)');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C727ACA70 FOREIGN KEY (parent_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE organization_service ADD CONSTRAINT FK_2C4F129132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE organization_service ADD CONSTRAINT FK_2C4F1291ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id)');
        $this->addSql('ALTER TABLE log.request_log ADD CONSTRAINT FK_A14398BAC69D3FB FOREIGN KEY (usr_id) REFERENCES usr (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2722827A FOREIGN KEY (service_group_id) REFERENCES services_group (id)');
        $this->addSql('ALTER TABLE user_data ADD CONSTRAINT FK_D772BFAAA76ED395 FOREIGN KEY (user_id) REFERENCES usr (id)');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT FK_5C844C5C69D3FB FOREIGN KEY (usr_id) REFERENCES usr (id)');
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
        $this->addSql('ALTER TABLE registration DROP CONSTRAINT FK_62A8A7A7E5B533F9');
        $this->addSql('ALTER TABLE department DROP CONSTRAINT FK_CD1DE18A32C8A3DE');
        $this->addSql('ALTER TABLE organization DROP CONSTRAINT FK_C1EE637C727ACA70');
        $this->addSql('ALTER TABLE organization_service DROP CONSTRAINT FK_2C4F129132C8A3DE');
        $this->addSql('ALTER TABLE usr DROP CONSTRAINT FK_1762498C32C8A3DE');
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F8444073B09');
        $this->addSql('ALTER TABLE organization_service DROP CONSTRAINT FK_2C4F1291ED5CA9E6');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD2722827A');
        $this->addSql('ALTER TABLE log.error_log DROP CONSTRAINT FK_F3565581C69D3FB');
        $this->addSql('ALTER TABLE log.request_log DROP CONSTRAINT FK_A14398BAC69D3FB');
        $this->addSql('ALTER TABLE user_data DROP CONSTRAINT FK_D772BFAAA76ED395');
        $this->addSql('ALTER TABLE user_settings DROP CONSTRAINT FK_5C844C5C69D3FB');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE log.error_log');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE organization_service');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE registration');
        $this->addSql('DROP TABLE log.request_log');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE services_group');
        $this->addSql('DROP TABLE user_data');
        $this->addSql('DROP TABLE user_settings');
        $this->addSql('DROP TABLE usr');
    }
}
