<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210517085212 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6432CA3EC69D3FB32C8A3DE ON user_rights (usr_id, organization_id) WHERE usr_id IS NOT NULL AND organization_id IS NOT NULL');
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
        $this->addSql('IF EXISTS (SELECT * FROM sysobjects WHERE name = \'UNIQ_6432CA3EC69D3FB32C8A3DE\')
            ALTER TABLE user_rights DROP CONSTRAINT UNIQ_6432CA3EC69D3FB32C8A3DE
        ELSE
            DROP INDEX UNIQ_6432CA3EC69D3FB32C8A3DE ON user_rights');
    }
}
