<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230304221515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notify (id INT AUTO_INCREMENT NOT NULL, rendezvous_id INT DEFAULT NULL, coach_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, INDEX IDX_217BEDC83345E0A3 (rendezvous_id), INDEX IDX_217BEDC83C105691 (coach_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notify ADD CONSTRAINT FK_217BEDC83345E0A3 FOREIGN KEY (rendezvous_id) REFERENCES rendez_vous (id)');
        $this->addSql('ALTER TABLE notify ADD CONSTRAINT FK_217BEDC83C105691 FOREIGN KEY (coach_id) REFERENCES coaching (id)');
        $this->addSql('ALTER TABLE coaching ADD dislike_button INT NOT NULL, ADD like_button INT NOT NULL, ADD desc_coach LONGTEXT NOT NULL, DROP nom_coach, DROP prenom_coach, DROP email_coach');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064044296D31F');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064044296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notify DROP FOREIGN KEY FK_217BEDC83345E0A3');
        $this->addSql('ALTER TABLE notify DROP FOREIGN KEY FK_217BEDC83C105691');
        $this->addSql('DROP TABLE notify');
        $this->addSql('ALTER TABLE coaching ADD nom_coach VARCHAR(255) NOT NULL, ADD prenom_coach VARCHAR(255) NOT NULL, ADD email_coach VARCHAR(255) NOT NULL, DROP dislike_button, DROP like_button, DROP desc_coach');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064044296D31F');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064044296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON UPDATE CASCADE ON DELETE SET NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE SET NULL');
    }
}
