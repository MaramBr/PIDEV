<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426200453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE companie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transporteur (id INT AUTO_INCREMENT NOT NULL, companies_id INT DEFAULT NULL, numero INT NOT NULL, prix INT NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_A25649756AE4741E (companies_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transporteur ADD CONSTRAINT FK_A25649756AE4741E FOREIGN KEY (companies_id) REFERENCES companie (id)');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064044296D31F');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064044296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transporteur DROP FOREIGN KEY FK_A25649756AE4741E');
        $this->addSql('DROP TABLE companie');
        $this->addSql('DROP TABLE transporteur');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064044296D31F');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064044296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON UPDATE SET NULL ON DELETE SET NULL');
    }
}
