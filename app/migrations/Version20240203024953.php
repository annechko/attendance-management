<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240203024953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE period_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE period (id INT NOT NULL, intake_id INT NOT NULL, name VARCHAR(255) NOT NULL, start DATE NOT NULL, finish DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C5B81ECE733DE450 ON period (intake_id)');
        $this->addSql('COMMENT ON COLUMN period.start IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN period.finish IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE period ADD CONSTRAINT FK_C5B81ECE733DE450 FOREIGN KEY (intake_id) REFERENCES intake (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE period_id_seq CASCADE');
        $this->addSql('ALTER TABLE period DROP CONSTRAINT FK_C5B81ECE733DE450');
        $this->addSql('DROP TABLE period');
    }
}
