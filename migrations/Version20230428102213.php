<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230428102213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_like DROP FOREIGN KEY FK_85FB3D5C4FD8F9C3');
        $this->addSql('DROP INDEX IDX_85FB3D5C4FD8F9C3 ON produit_like');
        $this->addSql('ALTER TABLE produit_like CHANGE produit_id_id produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit_like ADD CONSTRAINT FK_85FB3D5CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_85FB3D5CF347EFB ON produit_like (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_like DROP FOREIGN KEY FK_85FB3D5CF347EFB');
        $this->addSql('DROP INDEX IDX_85FB3D5CF347EFB ON produit_like');
        $this->addSql('ALTER TABLE produit_like CHANGE produit_id produit_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit_like ADD CONSTRAINT FK_85FB3D5C4FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_85FB3D5C4FD8F9C3 ON produit_like (produit_id_id)');
    }
}
