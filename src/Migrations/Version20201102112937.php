<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201102112937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metadata_catalog_themetaxonomies (catalog_metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_ED914358CD2AC8F7 (catalog_metadata_id), INDEX IDX_ED9143584ACEC524 (ontology_concept_id), PRIMARY KEY(catalog_metadata_id, ontology_concept_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_dataset_themes (dataset_metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_E5606EA54C040FE1 (dataset_metadata_id), INDEX IDX_E5606EA54ACEC524 (ontology_concept_id), PRIMARY KEY(dataset_metadata_id, ontology_concept_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_catalog_themetaxonomies ADD CONSTRAINT FK_ED914358CD2AC8F7 FOREIGN KEY (catalog_metadata_id) REFERENCES metadata_catalog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_catalog_themetaxonomies ADD CONSTRAINT FK_ED9143584ACEC524 FOREIGN KEY (ontology_concept_id) REFERENCES ontology_concept (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_dataset_themes ADD CONSTRAINT FK_E5606EA54C040FE1 FOREIGN KEY (dataset_metadata_id) REFERENCES metadata_dataset (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_dataset_themes ADD CONSTRAINT FK_E5606EA54ACEC524 FOREIGN KEY (ontology_concept_id) REFERENCES ontology_concept (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE metadata_catalog_themetaxonomies');
        $this->addSql('DROP TABLE metadata_dataset_themes');
    }
}
