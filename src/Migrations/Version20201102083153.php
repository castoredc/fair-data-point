<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201102083153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE catalog_themetaxonomies (catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_DE56F2C6CC3C66FC (catalog_id), INDEX IDX_DE56F2C64ACEC524 (ontology_concept_id), PRIMARY KEY(catalog_id, ontology_concept_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dataset_themes (dataset_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_7334E664D47C2D1B (dataset_id), INDEX IDX_7334E6644ACEC524 (ontology_concept_id), PRIMARY KEY(dataset_id, ontology_concept_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE catalog_themetaxonomies ADD CONSTRAINT FK_DE56F2C6CC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalog_themetaxonomies ADD CONSTRAINT FK_DE56F2C64ACEC524 FOREIGN KEY (ontology_concept_id) REFERENCES ontology_concept (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dataset_themes ADD CONSTRAINT FK_7334E664D47C2D1B FOREIGN KEY (dataset_id) REFERENCES dataset (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dataset_themes ADD CONSTRAINT FK_7334E6644ACEC524 FOREIGN KEY (ontology_concept_id) REFERENCES ontology_concept (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE catalog_themetaxonomies');
        $this->addSql('DROP TABLE dataset_themes');
    }
}
