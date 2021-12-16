<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211215152422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metadata_study_conditions (study_metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_4278168091AB1465 (study_metadata_id), INDEX IDX_427816804ACEC524 (ontology_concept_id), PRIMARY KEY(study_metadata_id, ontology_concept_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_study_conditions ADD CONSTRAINT FK_4278168091AB1465 FOREIGN KEY (study_metadata_id) REFERENCES metadata_study (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_study_conditions ADD CONSTRAINT FK_427816804ACEC524 FOREIGN KEY (ontology_concept_id) REFERENCES ontology_concept (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_study ADD keyword CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2B5A93713B FOREIGN KEY (keyword) REFERENCES text_localized (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_41C2F2B5A93713B ON metadata_study (keyword)');
        $this->addSql('ALTER TABLE metadata_study_centers CHANGE metadata metadata CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE organization organization CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study_team CHANGE metadata metadata CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE person person CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE metadata_study_conditions');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2B5A93713B');
        $this->addSql('DROP INDEX UNIQ_41C2F2B5A93713B ON metadata_study');
        $this->addSql('ALTER TABLE metadata_study DROP keyword');
        $this->addSql('ALTER TABLE metadata_study_centers CHANGE metadata metadata CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', CHANGE organization organization CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study_team CHANGE metadata metadata CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', CHANGE person person CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
    }
}
