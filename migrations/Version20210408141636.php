<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210408141636 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment (id INT IDENTITY NOT NULL, organization_service_id INT NOT NULL, date DATE NOT NULL, time_from TIME(0) NOT NULL, time_till TIME(0) NOT NULL, need_dinner BIT NOT NULL, dinner_from TIME(0), dinner_till TIME(0), duration INT NOT NULL, persons INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_FE38F8444073B09 ON appointment (organization_service_id)');
        $this->addSql('CREATE UNIQUE INDEX organization_service_date_unique_idx ON appointment (organization_service_id, date) WHERE organization_service_id IS NOT NULL AND date IS NOT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8444073B09 FOREIGN KEY (organization_service_id) REFERENCES organization_service (id)');
        $this->addSql('DROP TABLE organization_service_appointment');
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
        $this->addSql('CREATE TABLE organization_service_appointment (id INT IDENTITY NOT NULL, organization_service_id INT NOT NULL, date DATE NOT NULL, time_from TIME(0) NOT NULL, time_till TIME(0) NOT NULL, need_dinner BIT NOT NULL, dinner_from TIME(0), dinner_till TIME(0), duration INT NOT NULL, persons INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_AA11D4594073B09 ON organization_service_appointment (organization_service_id)');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX organization_service_date_unique_idx ON organization_service_appointment (organization_service_id, date) WHERE organization_service_id IS NOT NULL AND date IS NOT NULL');
        $this->addSql('ALTER TABLE organization_service_appointment ADD CONSTRAINT FK_AA11D4594073B09 FOREIGN KEY (organization_service_id) REFERENCES organization_service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE appointment');
    }
}
