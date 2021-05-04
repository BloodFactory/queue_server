<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210504075916 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_settings (id INT IDENTITY NOT NULL, usr_id INT NOT NULL, dark_mode BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C844C5C69D3FB ON user_settings (usr_id) WHERE usr_id IS NOT NULL');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT DF_5C844C5_EECE9A15 DEFAULT \'0\' FOR dark_mode');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT FK_5C844C5C69D3FB FOREIGN KEY (usr_id) REFERENCES usr (id)');
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) NOT NULL');
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
        $this->addSql('DROP TABLE user_settings');
        $this->addSql('ALTER TABLE log.error_log ALTER COLUMN message VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS NOT NULL');
    }
}
