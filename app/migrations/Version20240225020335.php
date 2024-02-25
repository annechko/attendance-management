<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240225020335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE attendance_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE attendance (id INT NOT NULL, student_id INT NOT NULL, teacher_id INT NOT NULL, subject_id INT NOT NULL, date DATE NOT NULL, status INT NOT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6DE30D91CB944F1A ON attendance (student_id)');
        $this->addSql('CREATE INDEX IDX_6DE30D9141807E1D ON attendance (teacher_id)');
        $this->addSql('CREATE INDEX IDX_6DE30D9123EDC87 ON attendance (subject_id)');
        $this->addSql('COMMENT ON COLUMN attendance.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D91CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D9141807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D9123EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE attendance_id_seq CASCADE');
        $this->addSql('ALTER TABLE attendance DROP CONSTRAINT FK_6DE30D91CB944F1A');
        $this->addSql('ALTER TABLE attendance DROP CONSTRAINT FK_6DE30D9141807E1D');
        $this->addSql('ALTER TABLE attendance DROP CONSTRAINT FK_6DE30D9123EDC87');
        $this->addSql('DROP TABLE attendance');
    }
}
