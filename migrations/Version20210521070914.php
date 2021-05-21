<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210521070914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) NOT NULL');
        $this->addSql('ALTER TABLE service ADD parent_id INT');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2727ACA70 FOREIGN KEY (parent_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD2727ACA70 ON service (parent_id)');
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
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS NOT NULL');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD2727ACA70');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'IDX_E19D9AD2727ACA70\')
                            ALTER TABLE service DROP CONSTRAINT IDX_E19D9AD2727ACA70
                        ELSE
                            DROP INDEX IDX_E19D9AD2727ACA70 ON service
                    ');
        $this->addSql('ALTER TABLE service DROP COLUMN parent_id');
    }
}
