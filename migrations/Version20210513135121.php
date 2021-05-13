<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210513135121 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) NOT NULL');
        $this->addSql('IF EXISTS (SELECT * FROM sysobjects WHERE name = \'UNIQ_C1EE637C5E237E06\')
            ALTER TABLE organization DROP CONSTRAINT UNIQ_C1EE637C5E237E06
        ELSE
            DROP INDEX UNIQ_C1EE637C5E237E06 ON organization');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C1EE637C727ACA705E237E06 ON organization (parent_id, name) WHERE parent_id IS NOT NULL AND name IS NOT NULL');
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
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS NOT NULL');
        $this->addSql('IF EXISTS (SELECT * FROM sysobjects WHERE name = \'UNIQ_C1EE637C727ACA705E237E06\')
            ALTER TABLE organization DROP CONSTRAINT UNIQ_C1EE637C727ACA705E237E06
        ELSE
            DROP INDEX UNIQ_C1EE637C727ACA705E237E06 ON organization');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UNIQ_C1EE637C5E237E06 ON organization (name) WHERE name IS NOT NULL');
    }
}
