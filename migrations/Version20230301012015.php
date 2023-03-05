<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230301012015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorie (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_7DE77163FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorie_produit (favorie_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_43957ED9249A8F58 (favorie_id), INDEX IDX_43957ED9F347EFB (produit_id), PRIMARY KEY(favorie_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorie ADD CONSTRAINT FK_7DE77163FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favorie_produit ADD CONSTRAINT FK_43957ED9249A8F58 FOREIGN KEY (favorie_id) REFERENCES favorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorie_produit ADD CONSTRAINT FK_43957ED9F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorie DROP FOREIGN KEY FK_7DE77163FB88E14F');
        $this->addSql('ALTER TABLE favorie_produit DROP FOREIGN KEY FK_43957ED9249A8F58');
        $this->addSql('ALTER TABLE favorie_produit DROP FOREIGN KEY FK_43957ED9F347EFB');
        $this->addSql('DROP TABLE favorie');
        $this->addSql('DROP TABLE favorie_produit');
    }
}
