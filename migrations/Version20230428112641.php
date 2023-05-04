<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230428112641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27A96778EC');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27A96778EC FOREIGN KEY (categorys_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE produit_like DROP FOREIGN KEY FK_85FB3D5CF347EFB');
        $this->addSql('ALTER TABLE produit_like ADD CONSTRAINT FK_85FB3D5CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C9F347EFB');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE transporteur ADD nom VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27A96778EC');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27A96778EC FOREIGN KEY (categorys_id) REFERENCES category (id) ON UPDATE SET NULL ON DELETE SET NULL');
        $this->addSql('ALTER TABLE produit_like DROP FOREIGN KEY FK_85FB3D5CF347EFB');
        $this->addSql('ALTER TABLE produit_like ADD CONSTRAINT FK_85FB3D5CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON UPDATE SET NULL ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C9F347EFB');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON UPDATE SET NULL ON DELETE SET NULL');
        $this->addSql('ALTER TABLE transporteur DROP nom');
    }
}
