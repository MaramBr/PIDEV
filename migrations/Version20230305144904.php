<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230305144904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notify DROP FOREIGN KEY FK_217BEDC83345E0A3');
        $this->addSql('ALTER TABLE notify DROP FOREIGN KEY FK_217BEDC83C105691');
        $this->addSql('ALTER TABLE notify ADD CONSTRAINT FK_217BEDC83345E0A3 FOREIGN KEY (rendezvous_id) REFERENCES rendez_vous (id)');
        $this->addSql('ALTER TABLE notify ADD CONSTRAINT FK_217BEDC83C105691 FOREIGN KEY (coach_id) REFERENCES coaching (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD etatrdv TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notify DROP FOREIGN KEY FK_217BEDC83345E0A3');
        $this->addSql('ALTER TABLE notify DROP FOREIGN KEY FK_217BEDC83C105691');
        $this->addSql('ALTER TABLE notify ADD CONSTRAINT FK_217BEDC83345E0A3 FOREIGN KEY (rendezvous_id) REFERENCES rendez_vous (id) ON UPDATE CASCADE ON DELETE SET NULL');
        $this->addSql('ALTER TABLE notify ADD CONSTRAINT FK_217BEDC83C105691 FOREIGN KEY (coach_id) REFERENCES coaching (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rendez_vous DROP etatrdv');
    }
}
