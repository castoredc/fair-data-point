<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200428144919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE annotation (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', entity VARCHAR(190) NOT NULL, concept VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', INDEX IDX_2E443EF2E284468 (entity), INDEX IDX_2E443EF2E74A6050 (concept), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ontology (url VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', name VARCHAR(255) NOT NULL, bio_portal_id VARCHAR(255) NOT NULL, PRIMARY KEY(url)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ontology_concept (url VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', ontology VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\', id VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, INDEX IDX_5972B8FFDAF05D3 (ontology), PRIMARY KEY(url)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annotation ADD CONSTRAINT FK_2E443EF2E284468 FOREIGN KEY (entity) REFERENCES castor_entity (id)');
        $this->addSql('ALTER TABLE annotation ADD CONSTRAINT FK_2E443EF2E74A6050 FOREIGN KEY (concept) REFERENCES ontology_concept (url)');
        $this->addSql('ALTER TABLE ontology_concept ADD CONSTRAINT FK_5972B8FFDAF05D3 FOREIGN KEY (ontology) REFERENCES ontology (url)');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_value ADD ontology VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\'');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_value ADD CONSTRAINT FK_2FD386FFDAF05D3 FOREIGN KEY (ontology) REFERENCES ontology (url)');
        $this->addSql('CREATE INDEX IDX_2FD386FFDAF05D3 ON rdf_triple_element_castor_value (ontology)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rdf_triple_element_castor_value DROP FOREIGN KEY FK_2FD386FFDAF05D3');
        $this->addSql('ALTER TABLE ontology_concept DROP FOREIGN KEY FK_5972B8FFDAF05D3');
        $this->addSql('ALTER TABLE annotation DROP FOREIGN KEY FK_2E443EF2E74A6050');
        $this->addSql('DROP TABLE annotation');
        $this->addSql('DROP TABLE ontology');
        $this->addSql('DROP TABLE ontology_concept');
        $this->addSql('DROP INDEX IDX_2FD386FFDAF05D3 ON rdf_triple_element_castor_value');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_value DROP ontology');
    }
}
