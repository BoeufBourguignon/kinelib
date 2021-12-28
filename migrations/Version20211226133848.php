<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211226133848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE edt (id INT AUTO_INCREMENT NOT NULL, kine_id INT NOT NULL, periode VARCHAR(10) NOT NULL, jour INT NOT NULL, heure_debut VARCHAR(5) NOT NULL, heure_fin VARCHAR(5) NOT NULL, INDEX IDX_E7A4CB5F88DC4BCC (kine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rdv (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, kine_id INT NOT NULL, heure_debut VARCHAR(5) NOT NULL, date DATE NOT NULL, INDEX IDX_10C31F86A76ED395 (user_id), INDEX IDX_10C31F8688DC4BCC (kine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE edt ADD CONSTRAINT FK_E7A4CB5F88DC4BCC FOREIGN KEY (kine_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F86A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F8688DC4BCC FOREIGN KEY (kine_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE edt');
        $this->addSql('DROP TABLE rdv');
    }
}
