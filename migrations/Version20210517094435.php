<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210517094435 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) NOT NULL');
        $this->addSql('ALTER TABLE user_rights ALTER COLUMN v BIT NOT NULL');
        $this->addSql('ALTER TABLE user_rights ADD CONSTRAINT DF_6432CA3E_6B643B84 DEFAULT \'0\' FOR v');
        $this->addSql('ALTER TABLE user_rights ALTER COLUMN e BIT NOT NULL');
        $this->addSql('ALTER TABLE user_rights ADD CONSTRAINT DF_6432CA3E_EFDA7A5A DEFAULT \'0\' FOR e');
        $this->addSql('ALTER TABLE user_rights ALTER COLUMN d BIT NOT NULL');
        $this->addSql('ALTER TABLE user_rights ADD CONSTRAINT DF_6432CA3E_98DD4ACC DEFAULT \'0\' FOR d');
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
        $this->addSql('ALTER TABLE user_rights DROP CONSTRAINT DF_6432CA3E_6B643B84');
        $this->addSql('ALTER TABLE user_rights ALTER COLUMN v BIT NOT NULL');
        $this->addSql('ALTER TABLE user_rights DROP CONSTRAINT DF_6432CA3E_EFDA7A5A');
        $this->addSql('ALTER TABLE user_rights ALTER COLUMN e BIT NOT NULL');
        $this->addSql('ALTER TABLE user_rights DROP CONSTRAINT DF_6432CA3E_98DD4ACC');
        $this->addSql('ALTER TABLE user_rights ALTER COLUMN d BIT NOT NULL');
    }
}
