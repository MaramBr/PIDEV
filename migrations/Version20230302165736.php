<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230302165736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notify ADD rendezvous_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notify ADD CONSTRAINT FK_217BEDC83345E0A3 FOREIGN KEY (rendezvous_id) REFERENCES rendez_vous (id)');
        $this->addSql('CREATE INDEX IDX_217BEDC83345E0A3 ON notify (rendezvous_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notify DROP FOREIGN KEY FK_217BEDC83345E0A3');
        $this->addSql('DROP INDEX IDX_217BEDC83345E0A3 ON notify');
        $this->addSql('ALTER TABLE notify DROP rendezvous_id');
    }
}
