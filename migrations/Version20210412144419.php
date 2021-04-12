<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210412144419 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE registration (id INT IDENTITY NOT NULL, appointment_id INT NOT NULL, time TIME(0) NOT NULL, last_name NVARCHAR(50) NOT NULL, first_name NVARCHAR(50) NOT NULL, middle_name NVARCHAR(50) NOT NULL, birthday DATE NOT NULL, phone NVARCHAR(21), email NVARCHAR(255), status BIT, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_62A8A7A7E5B533F9 ON registration (appointment_id)');
        $this->addSql('CREATE INDEX person_idx ON registration (birthday, last_name, first_name, middle_name)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id)');
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
        $this->addSql('DROP TABLE registration');
    }
}
