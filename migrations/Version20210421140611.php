<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210421140611 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA log');
        $this->addSql('CREATE TABLE log.request_log (id UNIQUEIDENTIFIER NOT NULL, usr_id INT, moment DATETIME2(6) NOT NULL, content VARCHAR(MAX), method NVARCHAR(15) NOT NULL, path NVARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_A14398BAC69D3FB ON log.request_log (usr_id)');
        $this->addSql('CREATE INDEX user_path_idx ON log.request_log (usr_id, path)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'log\', N\'TABLE\', \'request_log\', N\'COLUMN\', content');
        $this->addSql('ALTER TABLE log.request_log ADD CONSTRAINT FK_A14398BAC69D3FB FOREIGN KEY (usr_id) REFERENCES usr (id)');
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
        $this->addSql('DROP TABLE log.request_log');
    }
}
