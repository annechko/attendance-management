<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240203014339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE intake_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE intake (id INT NOT NULL, course_id INT NOT NULL, name VARCHAR(255) NOT NULL, start DATE NOT NULL, finish DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AEA2877A591CC992 ON intake (course_id)');
        $this->addSql('COMMENT ON COLUMN intake.start IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN intake.finish IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE intake ADD CONSTRAINT FK_AEA2877A591CC992 FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE intake_id_seq CASCADE');
        $this->addSql('ALTER TABLE intake DROP CONSTRAINT FK_AEA2877A591CC992');
        $this->addSql('DROP TABLE intake');
    }
}
