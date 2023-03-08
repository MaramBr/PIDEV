<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306175411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments CHANGE active active TINYINT(1) NOT NULL, CHANGE rgpd rgpd TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE notify DROP FOREIGN KEY FK_217BEDC83345E0A3');
        $this->addSql('ALTER TABLE notify ADD CONSTRAINT FK_217BEDC83345E0A3 FOREIGN KEY (rendezvous_id) REFERENCES rendez_vous (id)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27A96778EC');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27A96778EC FOREIGN KEY (categorys_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE reclamation CHANGE description_r description_r VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A58B8AC59');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A58B8AC59 FOREIGN KEY (coachings_id) REFERENCES coaching (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments CHANGE active active TINYINT(1) DEFAULT NULL, CHANGE rgpd rgpd TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE notify DROP FOREIGN KEY FK_217BEDC83345E0A3');
        $this->addSql('ALTER TABLE notify ADD CONSTRAINT FK_217BEDC83345E0A3 FOREIGN KEY (rendezvous_id) REFERENCES rendez_vous (id) ON UPDATE SET NULL ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27A96778EC');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27A96778EC FOREIGN KEY (categorys_id) REFERENCES category (id) ON UPDATE SET NULL ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reclamation CHANGE description_r description_r LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A58B8AC59');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A58B8AC59 FOREIGN KEY (coachings_id) REFERENCES coaching (id) ON UPDATE SET NULL ON DELETE CASCADE');
    }
}
