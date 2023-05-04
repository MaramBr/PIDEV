<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230428101629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C9CD11A2CF');
        $this->addSql('DROP INDEX IDX_CEB607C9CD11A2CF ON ratings');
        $this->addSql('ALTER TABLE ratings CHANGE produits_id id_produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9AABEFE2C FOREIGN KEY (id_produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_CEB607C9AABEFE2C ON ratings (id_produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C9AABEFE2C');
        $this->addSql('DROP INDEX IDX_CEB607C9AABEFE2C ON ratings');
        $this->addSql('ALTER TABLE ratings CHANGE id_produit_id produits_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9CD11A2CF FOREIGN KEY (produits_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_CEB607C9CD11A2CF ON ratings (produits_id)');
    }
}
