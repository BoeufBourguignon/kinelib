<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211226101947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rdv (kine_id INT NOT NULL, user_id INT NOT NULL, heure_debut VARCHAR(5) NOT NULL, INDEX IDX_10C31F8688DC4BCC (kine_id), INDEX IDX_10C31F86A76ED395 (user_id), PRIMARY KEY(kine_id, user_id, heure_debut)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F8688DC4BCC FOREIGN KEY (kine_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F86A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE edt DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE edt ADD PRIMARY KEY (periode, jour, id_kine_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rdv');
        $this->addSql('ALTER TABLE edt DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE edt ADD PRIMARY KEY (id_kine_id, periode, jour)');
    }
}
