<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210527071301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F8444073B09');
        $this->addSql('DROP TABLE organization_service');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'IDX_FE38F8444073B09\')
                            ALTER TABLE appointment DROP CONSTRAINT IDX_FE38F8444073B09
                        ELSE
                            DROP INDEX IDX_FE38F8444073B09 ON appointment
                    ');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'organization_service_date_unique_idx\')
                            ALTER TABLE appointment DROP CONSTRAINT organization_service_date_unique_idx
                        ELSE
                            DROP INDEX organization_service_date_unique_idx ON appointment
                    ');
        $this->addSql('sp_rename \'appointment.organization_service_id\', \'organization_id\', \'COLUMN\'');
        $this->addSql('ALTER TABLE appointment ADD service_id INT NOT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F84432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_FE38F84432C8A3DE ON appointment (organization_id)');
        $this->addSql('CREATE INDEX IDX_FE38F844ED5CA9E6 ON appointment (service_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE38F84432C8A3DEED5CA9E6AA9E377A ON appointment (organization_id, service_id, date) WHERE organization_id IS NOT NULL AND service_id IS NOT NULL AND date IS NOT NULL');
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) NOT NULL');
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
        $this->addSql('CREATE TABLE organization_service (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_2C4F129132C8A3DE ON organization_service (organization_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_2C4F1291ED5CA9E6 ON organization_service (service_id)');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX organization_service_unique_idx ON organization_service (organization_id, service_id) WHERE organization_id IS NOT NULL AND service_id IS NOT NULL');
        $this->addSql('ALTER TABLE organization_service ADD CONSTRAINT FK_2C4F129132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE organization_service ADD CONSTRAINT FK_2C4F1291ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F84432C8A3DE');
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F844ED5CA9E6');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'IDX_FE38F84432C8A3DE\')
                            ALTER TABLE appointment DROP CONSTRAINT IDX_FE38F84432C8A3DE
                        ELSE
                            DROP INDEX IDX_FE38F84432C8A3DE ON appointment
                    ');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'IDX_FE38F844ED5CA9E6\')
                            ALTER TABLE appointment DROP CONSTRAINT IDX_FE38F844ED5CA9E6
                        ELSE
                            DROP INDEX IDX_FE38F844ED5CA9E6 ON appointment
                    ');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'UNIQ_FE38F84432C8A3DEED5CA9E6AA9E377A\')
                            ALTER TABLE appointment DROP CONSTRAINT UNIQ_FE38F84432C8A3DEED5CA9E6AA9E377A
                        ELSE
                            DROP INDEX UNIQ_FE38F84432C8A3DEED5CA9E6AA9E377A ON appointment
                    ');
        $this->addSql('ALTER TABLE appointment ADD organization_service_id INT NOT NULL');
        $this->addSql('ALTER TABLE appointment DROP COLUMN organization_id');
        $this->addSql('ALTER TABLE appointment DROP COLUMN service_id');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8444073B09 FOREIGN KEY (organization_service_id) REFERENCES organization_service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_FE38F8444073B09 ON appointment (organization_service_id)');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX organization_service_date_unique_idx ON appointment (organization_service_id, date) WHERE organization_service_id IS NOT NULL AND date IS NOT NULL');
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS NOT NULL');
    }
}
