<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210520141637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD2722827A');
        $this->addSql('DROP TABLE services_group');
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) NOT NULL');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'IDX_E19D9AD2722827A\')
                            ALTER TABLE service DROP CONSTRAINT IDX_E19D9AD2722827A
                        ELSE
                            DROP INDEX IDX_E19D9AD2722827A ON service
                    ');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'UNIQ_E19D9AD2722827A5E237E06\')
                            ALTER TABLE service DROP CONSTRAINT UNIQ_E19D9AD2722827A5E237E06
                        ELSE
                            DROP INDEX UNIQ_E19D9AD2722827A5E237E06 ON service
                    ');
        $this->addSql('ALTER TABLE service DROP COLUMN service_group_id');
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
        $this->addSql('CREATE TABLE services_group (id INT IDENTITY NOT NULL, name NVARCHAR(255) COLLATE Cyrillic_General_CI_AS NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UNIQ_D122FE875E237E06 ON services_group (name) WHERE name IS NOT NULL');
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS NOT NULL');
        $this->addSql('ALTER TABLE service ADD service_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2722827A FOREIGN KEY (service_group_id) REFERENCES services_group (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_E19D9AD2722827A ON service (service_group_id)');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UNIQ_E19D9AD2722827A5E237E06 ON service (service_group_id, name) WHERE service_group_id IS NOT NULL AND name IS NOT NULL');
    }
}
