<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211223092356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE edt (id_kine_id INT NOT NULL, periode VARCHAR(10) NOT NULL, jour INT NOT NULL, heure_debut VARCHAR(5) NOT NULL, heure_fin VARCHAR(5) NOT NULL, INDEX IDX_E7A4CB5F5641E2BC (id_kine_id), PRIMARY KEY(id_kine_id, periode, jour)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE edt ADD CONSTRAINT FK_E7A4CB5F5641E2BC FOREIGN KEY (id_kine_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE edt');
    }
}
