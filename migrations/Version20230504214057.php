<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230504214057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE companie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit_like (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, INDEX IDX_85FB3D5CF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ratings (id_rating INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, user_name VARCHAR(255) NOT NULL, rating DOUBLE PRECISION NOT NULL, INDEX IDX_CEB607C9F347EFB (produit_id), PRIMARY KEY(id_rating)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transporteur (id INT AUTO_INCREMENT NOT NULL, companies_id INT DEFAULT NULL, numero INT NOT NULL, prix INT NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_A25649756AE4741E (companies_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit_like ADD CONSTRAINT FK_85FB3D5CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE transporteur ADD CONSTRAINT FK_A25649756AE4741E FOREIGN KEY (companies_id) REFERENCES companie (id)');
        $this->addSql('ALTER TABLE user CHANGE is_active is_active TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_like DROP FOREIGN KEY FK_85FB3D5CF347EFB');
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C9F347EFB');
        $this->addSql('ALTER TABLE transporteur DROP FOREIGN KEY FK_A25649756AE4741E');
        $this->addSql('DROP TABLE companie');
        $this->addSql('DROP TABLE produit_like');
        $this->addSql('DROP TABLE ratings');
        $this->addSql('DROP TABLE transporteur');
        $this->addSql('ALTER TABLE user CHANGE is_active is_active TINYINT(1) DEFAULT NULL');
    }
}
