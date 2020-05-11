<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200511143725 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rdf_prefix (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', prefix VARCHAR(255) NOT NULL, uri VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', INDEX IDX_F1D3FA34A4483781 (distribution), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rdf_prefix ADD CONSTRAINT FK_F1D3FA34A4483781 FOREIGN KEY (distribution) REFERENCES distribution_rdf (distribution)');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_entity CHANGE entity entity VARCHAR(190) NOT NULL');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_value CHANGE field field VARCHAR(190) NOT NULL');
        $this->addSql('ALTER TABLE ontology_concept CHANGE ontology ontology VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE rdf_prefix');
        $this->addSql('ALTER TABLE ontology_concept CHANGE ontology ontology VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:iri)\'');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_entity CHANGE entity entity VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_value CHANGE field field VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
    }
}
