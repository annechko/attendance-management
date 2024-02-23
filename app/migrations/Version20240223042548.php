<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240223042548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function preUp(Schema $schema): void
    {
        parent::preUp($schema);
        $notUniqueNamesCount = $this->connection->createQueryBuilder()
            ->from('institution')
            ->select('1')
            ->groupBy('name')
            ->setMaxResults(1)
            ->having('COUNT(id)>1')
            ->executeQuery();
        if ($notUniqueNamesCount->fetchOne() !== false) {
            $this->write('Not unique names, updating.');
            $this->addSql('UPDATE institution SET name = concat(name, \' \', id)');
        }
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A9F98E55E237E06 ON institution (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_3A9F98E55E237E06');
    }
}
