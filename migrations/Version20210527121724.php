<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210527121724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE department');
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
        $this->addSql('CREATE TABLE department (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, name NVARCHAR(255) COLLATE Cyrillic_General_CI_AS NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_CD1DE18A32C8A3DE ON department (organization_id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS NOT NULL');
    }
}
