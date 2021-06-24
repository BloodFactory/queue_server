<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210610113232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA log');
        $this->addSql('CREATE TABLE appointment (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, service_id INT NOT NULL, date DATE NOT NULL, time_from TIME(0) NOT NULL, time_till TIME(0) NOT NULL, need_dinner BIT NOT NULL, dinner_from TIME(0), dinner_till TIME(0), duration INT NOT NULL, persons INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_FE38F84432C8A3DE ON appointment (organization_id)');
        $this->addSql('CREATE INDEX IDX_FE38F844ED5CA9E6 ON appointment (service_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE38F84432C8A3DEED5CA9E6AA9E377A ON appointment (organization_id, service_id, date) WHERE organization_id IS NOT NULL AND service_id IS NOT NULL AND date IS NOT NULL');
        $this->addSql('CREATE TABLE appointment_template (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, service_id INT NOT NULL, time_from TIME(0) NOT NULL, time_till TIME(0) NOT NULL, need_dinner BIT NOT NULL, dinner_from TIME(0), dinner_till TIME(0), duration INT NOT NULL, persons INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_A244944B32C8A3DE ON appointment_template (organization_id)');
        $this->addSql('CREATE INDEX IDX_A244944BED5CA9E6 ON appointment_template (service_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A244944BED5CA9E632C8A3DE ON appointment_template (service_id, organization_id) WHERE service_id IS NOT NULL AND organization_id IS NOT NULL');
        $this->addSql('CREATE TABLE log.error_log (id UNIQUEIDENTIFIER NOT NULL, usr_id INT, message VARCHAR(MAX) NOT NULL, code INT NOT NULL, moment DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX usr_idx ON log.error_log (usr_id)');
        $this->addSql('CREATE TABLE organization (id INT IDENTITY NOT NULL, parent_id INT, name NVARCHAR(4000) NOT NULL, timezone SMALLINT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C1EE637C727ACA70 ON organization (parent_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C1EE637C727ACA705E237E06 ON organization (parent_id, name) WHERE parent_id IS NOT NULL AND name IS NOT NULL');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT DF_C1EE637C_3701B297 DEFAULT 3 FOR timezone');
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
        $this->addSql('CREATE TABLE service (id INT IDENTITY NOT NULL, service_group_id INT, name NVARCHAR(4000) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_E19D9AD2722827A ON service (service_group_id)');
        $this->addSql('CREATE TABLE service_group (id INT IDENTITY NOT NULL, parent_id INT, name NVARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C4B2A922727ACA70 ON service_group (parent_id)');
        $this->addSql('CREATE TABLE user_data (id INT IDENTITY NOT NULL, user_id INT NOT NULL, last_name NVARCHAR(50) NOT NULL, first_name NVARCHAR(50) NOT NULL, middle_name NVARCHAR(50), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D772BFAAA76ED395 ON user_data (user_id) WHERE user_id IS NOT NULL');
        $this->addSql('CREATE TABLE user_rights (id INT IDENTITY NOT NULL, usr_id INT NOT NULL, organization_id INT NOT NULL, v BIT NOT NULL, e BIT NOT NULL, d BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_6432CA3EC69D3FB ON user_rights (usr_id)');
        $this->addSql('CREATE INDEX IDX_6432CA3E32C8A3DE ON user_rights (organization_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6432CA3EC69D3FB32C8A3DE ON user_rights (usr_id, organization_id) WHERE usr_id IS NOT NULL AND organization_id IS NOT NULL');
        $this->addSql('ALTER TABLE user_rights ADD CONSTRAINT DF_6432CA3E_6B643B84 DEFAULT \'0\' FOR v');
        $this->addSql('ALTER TABLE user_rights ADD CONSTRAINT DF_6432CA3E_EFDA7A5A DEFAULT \'0\' FOR e');
        $this->addSql('ALTER TABLE user_rights ADD CONSTRAINT DF_6432CA3E_98DD4ACC DEFAULT \'0\' FOR d');
        $this->addSql('CREATE TABLE user_settings (id INT IDENTITY NOT NULL, usr_id INT NOT NULL, dark_mode BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C844C5C69D3FB ON user_settings (usr_id) WHERE usr_id IS NOT NULL');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT DF_5C844C5_EECE9A15 DEFAULT \'0\' FOR dark_mode');
        $this->addSql('CREATE TABLE usr (id INT IDENTITY NOT NULL, username NVARCHAR(180) NOT NULL, roles VARCHAR(MAX) NOT NULL, password NVARCHAR(255) NOT NULL, is_active BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1762498CF85E0677 ON usr (username) WHERE username IS NOT NULL');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'usr\', N\'COLUMN\', roles');
        $this->addSql('ALTER TABLE usr ADD CONSTRAINT DF_1762498C_1B5771DD DEFAULT \'1\' FOR is_active');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F84432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE appointment_template ADD CONSTRAINT FK_A244944B32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE appointment_template ADD CONSTRAINT FK_A244944BED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE log.error_log ADD CONSTRAINT FK_F3565581C69D3FB FOREIGN KEY (usr_id) REFERENCES usr (id)');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C727ACA70 FOREIGN KEY (parent_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id)');
        $this->addSql('ALTER TABLE log.request_log ADD CONSTRAINT FK_A14398BAC69D3FB FOREIGN KEY (usr_id) REFERENCES usr (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2722827A FOREIGN KEY (service_group_id) REFERENCES service_group (id)');
        $this->addSql('ALTER TABLE service_group ADD CONSTRAINT FK_C4B2A922727ACA70 FOREIGN KEY (parent_id) REFERENCES service_group (id)');
        $this->addSql('ALTER TABLE user_data ADD CONSTRAINT FK_D772BFAAA76ED395 FOREIGN KEY (user_id) REFERENCES usr (id)');
        $this->addSql('ALTER TABLE user_rights ADD CONSTRAINT FK_6432CA3EC69D3FB FOREIGN KEY (usr_id) REFERENCES usr (id)');
        $this->addSql('ALTER TABLE user_rights ADD CONSTRAINT FK_6432CA3E32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT FK_5C844C5C69D3FB FOREIGN KEY (usr_id) REFERENCES usr (id)');
    }

    public function down(Schema $schema): void
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
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F84432C8A3DE');
        $this->addSql('ALTER TABLE appointment_template DROP CONSTRAINT FK_A244944B32C8A3DE');
        $this->addSql('ALTER TABLE organization DROP CONSTRAINT FK_C1EE637C727ACA70');
        $this->addSql('ALTER TABLE user_rights DROP CONSTRAINT FK_6432CA3E32C8A3DE');
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F844ED5CA9E6');
        $this->addSql('ALTER TABLE appointment_template DROP CONSTRAINT FK_A244944BED5CA9E6');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD2722827A');
        $this->addSql('ALTER TABLE service_group DROP CONSTRAINT FK_C4B2A922727ACA70');
        $this->addSql('ALTER TABLE log.error_log DROP CONSTRAINT FK_F3565581C69D3FB');
        $this->addSql('ALTER TABLE log.request_log DROP CONSTRAINT FK_A14398BAC69D3FB');
        $this->addSql('ALTER TABLE user_data DROP CONSTRAINT FK_D772BFAAA76ED395');
        $this->addSql('ALTER TABLE user_rights DROP CONSTRAINT FK_6432CA3EC69D3FB');
        $this->addSql('ALTER TABLE user_settings DROP CONSTRAINT FK_5C844C5C69D3FB');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE appointment_template');
        $this->addSql('DROP TABLE log.error_log');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE registration');
        $this->addSql('DROP TABLE log.request_log');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_group');
        $this->addSql('DROP TABLE user_data');
        $this->addSql('DROP TABLE user_rights');
        $this->addSql('DROP TABLE user_settings');
        $this->addSql('DROP TABLE usr');
    }
}
