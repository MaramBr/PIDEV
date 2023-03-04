<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228134917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participant_evenement (participant_id INT NOT NULL, evenement_id INT NOT NULL, INDEX IDX_C824A73A9D1C3019 (participant_id), INDEX IDX_C824A73AFD02F13 (evenement_id), PRIMARY KEY(participant_id, evenement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participant_evenement ADD CONSTRAINT FK_C824A73A9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant_evenement ADD CONSTRAINT FK_C824A73AFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement ADD nb_participant VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant_evenement DROP FOREIGN KEY FK_C824A73A9D1C3019');
        $this->addSql('ALTER TABLE participant_evenement DROP FOREIGN KEY FK_C824A73AFD02F13');
        $this->addSql('DROP TABLE participant_evenement');
        $this->addSql('ALTER TABLE evenement DROP nb_participant');
    }
}
