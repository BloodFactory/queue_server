<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210407151349 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('EXEC sp_rename N\'organization_service.organization_service_uniqu_idx\', N\'organization_service_unique_idx\', N\'INDEX\'');
        $this->addSql('CREATE UNIQUE INDEX organization_service_date_unique_idx ON organization_service_appointment (organization_service_id, date) WHERE organization_service_id IS NOT NULL AND date IS NOT NULL');
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
        $this->addSql('EXEC sp_rename N\'organization_service.organization_service_unique_idx\', N\'organization_service_uniqu_idx\', N\'INDEX\'');
        $this->addSql('IF EXISTS (SELECT * FROM sysobjects WHERE name = \'organization_service_date_unique_idx\')
            ALTER TABLE organization_service_appointment DROP CONSTRAINT organization_service_date_unique_idx
        ELSE
            DROP INDEX organization_service_date_unique_idx ON organization_service_appointment');
    }
}
