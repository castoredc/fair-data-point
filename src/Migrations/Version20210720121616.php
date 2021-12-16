<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720121616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO metadata_study_team (id, metadata, person, created_at, is_contact) SELECT uuid(), study_contacts.study_metadata_id, person.id, NOW(), 1
FROM study_contacts
JOIN agent agent_person on study_contacts.agent_id = agent_person.id
JOIN person on agent_person.id = person.id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
