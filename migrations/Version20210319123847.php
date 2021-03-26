<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210319123847 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_data (id INT IDENTITY NOT NULL, last_name NVARCHAR(50) NOT NULL, first_name NVARCHAR(50) NOT NULL, middle_name NVARCHAR(50), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE usr ADD user_data_id INT');
        $this->addSql('ALTER TABLE usr ADD CONSTRAINT FK_1762498C6FF8BF36 FOREIGN KEY (user_data_id) REFERENCES user_data (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1762498C6FF8BF36 ON usr (user_data_id) WHERE user_data_id IS NOT NULL');
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
        $this->addSql('ALTER TABLE usr DROP CONSTRAINT FK_1762498C6FF8BF36');
        $this->addSql('DROP TABLE user_data');
        $this->addSql('IF EXISTS (SELECT * FROM sysobjects WHERE name = \'UNIQ_1762498C6FF8BF36\')
            ALTER TABLE usr DROP CONSTRAINT UNIQ_1762498C6FF8BF36
        ELSE
            DROP INDEX UNIQ_1762498C6FF8BF36 ON usr');
        $this->addSql('ALTER TABLE usr DROP COLUMN user_data_id');
    }
}
