<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210429120244 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) NOT NULL');
        $this->addSql('ALTER TABLE log.request_log ADD query VARCHAR(MAX)');
        $this->addSql('ALTER TABLE log.request_log ADD request VARCHAR(MAX)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'log\', N\'TABLE\', \'request_log\', N\'COLUMN\', query');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'log\', N\'TABLE\', \'request_log\', N\'COLUMN\', request');
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
        $this->addSql('ALTER TABLE log.request_log DROP COLUMN query');
        $this->addSql('ALTER TABLE log.request_log DROP COLUMN request');
    }
}
