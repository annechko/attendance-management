<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240203082753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE period_to_subject_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE period_to_subject (id INT NOT NULL, subject_id INT NOT NULL, period_id INT NOT NULL, total_number_of_lessons INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1D5F6E0C23EDC87 ON period_to_subject (subject_id)');
        $this->addSql('CREATE INDEX IDX_1D5F6E0CEC8B7ADE ON period_to_subject (period_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D5F6E0CEC8B7ADE23EDC87 ON period_to_subject (period_id, subject_id)');
        $this->addSql('ALTER TABLE period_to_subject ADD CONSTRAINT FK_1D5F6E0C23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE period_to_subject ADD CONSTRAINT FK_1D5F6E0CEC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE period_to_subject_id_seq CASCADE');
        $this->addSql('ALTER TABLE period_to_subject DROP CONSTRAINT FK_1D5F6E0C23EDC87');
        $this->addSql('ALTER TABLE period_to_subject DROP CONSTRAINT FK_1D5F6E0CEC8B7ADE');
        $this->addSql('DROP TABLE period_to_subject');
    }
}
