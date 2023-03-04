<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220100543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coaching DROP nom_coach, DROP prenom_coach, DROP email_coach');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A58B8AC59');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A58B8AC59 FOREIGN KEY (coachings_id) REFERENCES coaching (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coaching ADD nom_coach VARCHAR(255) NOT NULL, ADD prenom_coach VARCHAR(255) NOT NULL, ADD email_coach VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A58B8AC59');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A58B8AC59 FOREIGN KEY (coachings_id) REFERENCES coaching (id) ON UPDATE CASCADE ON DELETE SET NULL');
    }
}
