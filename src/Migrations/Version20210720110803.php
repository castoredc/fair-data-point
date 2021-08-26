<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720110803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO metadata_study_centers (id, metadata, organization, created_at) SELECT agent_department.id, study_centers.study_metadata_id, organization.id, NOW()
FROM study_centers
JOIN agent agent_department on study_centers.agent_id = agent_department.id
JOIN department on agent_department.id = department.id
JOIN organization on department.organization = organization.id');

        $this->addSql('INSERT INTO metadata_study_centers_departments (participating_center_id, department_id) SELECT department.id, department.id
FROM study_centers
JOIN agent agent_department on study_centers.agent_id = agent_department.id
JOIN department on agent_department.id = department.id
JOIN organization on department.organization = organization.id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
