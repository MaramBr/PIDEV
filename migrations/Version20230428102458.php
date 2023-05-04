<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230428102458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C9AABEFE2C');
        $this->addSql('DROP INDEX IDX_CEB607C9AABEFE2C ON ratings');
        $this->addSql('ALTER TABLE ratings CHANGE id_produit_id produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_CEB607C9F347EFB ON ratings (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C9F347EFB');
        $this->addSql('DROP INDEX IDX_CEB607C9F347EFB ON ratings');
        $this->addSql('ALTER TABLE ratings CHANGE produit_id id_produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9AABEFE2C FOREIGN KEY (id_produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_CEB607C9AABEFE2C ON ratings (id_produit_id)');
    }
}
