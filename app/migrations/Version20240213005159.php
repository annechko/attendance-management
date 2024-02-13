<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213005159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE teacher_to_subject_to_intake_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE teacher_to_subject_to_intake (id INT NOT NULL, subject_id INT NOT NULL, teacher_id INT NOT NULL, intake_id INT NOT NULL, start DATE NOT NULL, finish DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2C8177FE23EDC87 ON teacher_to_subject_to_intake (subject_id)');
        $this->addSql('CREATE INDEX IDX_2C8177FE41807E1D ON teacher_to_subject_to_intake (teacher_id)');
        $this->addSql('CREATE INDEX IDX_2C8177FE733DE450 ON teacher_to_subject_to_intake (intake_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2C8177FE41807E1D23EDC87733DE450 ON teacher_to_subject_to_intake (teacher_id, subject_id, intake_id)');
        $this->addSql('COMMENT ON COLUMN teacher_to_subject_to_intake.start IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN teacher_to_subject_to_intake.finish IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE teacher_to_subject_to_intake ADD CONSTRAINT FK_2C8177FE23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE teacher_to_subject_to_intake ADD CONSTRAINT FK_2C8177FE41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE teacher_to_subject_to_intake ADD CONSTRAINT FK_2C8177FE733DE450 FOREIGN KEY (intake_id) REFERENCES intake (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE teacher_to_subject_to_intake_id_seq CASCADE');
        $this->addSql('ALTER TABLE teacher_to_subject_to_intake DROP CONSTRAINT FK_2C8177FE23EDC87');
        $this->addSql('ALTER TABLE teacher_to_subject_to_intake DROP CONSTRAINT FK_2C8177FE41807E1D');
        $this->addSql('ALTER TABLE teacher_to_subject_to_intake DROP CONSTRAINT FK_2C8177FE733DE450');
        $this->addSql('DROP TABLE teacher_to_subject_to_intake');
    }
}
