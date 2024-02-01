<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240131065704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE admin_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE teacher_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76E7927C74 ON admin (email)');
        $this->addSql('CREATE TABLE teacher (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B0F6A6D5E7927C74 ON teacher (email)');
        $this->addSql('DELETE FROM student');
        $this->addSql('ALTER TABLE student ADD email VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE student ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE student ADD password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B723AF33E7927C74 ON student (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE admin_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE teacher_id_seq CASCADE');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('DROP INDEX UNIQ_B723AF33E7927C74');
        $this->addSql('ALTER TABLE student DROP email');
        $this->addSql('ALTER TABLE student DROP roles');
        $this->addSql('ALTER TABLE student DROP password');
    }
}
