<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720123430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE study_centers');
        $this->addSql('DROP TABLE study_contacts');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE study_centers (study_metadata_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', agent_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', INDEX IDX_1258265391AB1465 (study_metadata_id), INDEX IDX_125826533414710B (agent_id), PRIMARY KEY(study_metadata_id, agent_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE study_contacts (study_metadata_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', agent_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', INDEX IDX_90C900FA91AB1465 (study_metadata_id), INDEX IDX_90C900FA3414710B (agent_id), PRIMARY KEY(study_metadata_id, agent_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE study_centers ADD CONSTRAINT FK_125826533414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_centers ADD CONSTRAINT FK_1258265391AB1465 FOREIGN KEY (study_metadata_id) REFERENCES metadata_study (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_contacts ADD CONSTRAINT FK_90C900FA3414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_contacts ADD CONSTRAINT FK_90C900FA91AB1465 FOREIGN KEY (study_metadata_id) REFERENCES metadata_study (id) ON DELETE CASCADE');
    }
}
