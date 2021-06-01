<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210531093324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment_template (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, service_id INT NOT NULL, time_from TIME(0) NOT NULL, time_till TIME(0) NOT NULL, need_dinner BIT NOT NULL, dinner_from TIME(0), dinner_till TIME(0), duration INT NOT NULL, persons INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_A244944B32C8A3DE ON appointment_template (organization_id)');
        $this->addSql('CREATE INDEX IDX_A244944BED5CA9E6 ON appointment_template (service_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A244944BED5CA9E632C8A3DE ON appointment_template (service_id, organization_id) WHERE service_id IS NOT NULL AND organization_id IS NOT NULL');
        $this->addSql('ALTER TABLE appointment_template ADD CONSTRAINT FK_A244944B32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE appointment_template ADD CONSTRAINT FK_A244944BED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
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
        $this->addSql('DROP TABLE appointment_template');
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS NOT NULL');
    }
}
