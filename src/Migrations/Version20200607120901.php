<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200607120901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM distribution_rdf_modules');
        $this->addSql('DELETE FROM distribution_rdf');

        $this->addSql('CREATE TABLE castor_entity (id VARCHAR(190) NOT NULL, study_id VARCHAR(190) DEFAULT NULL, parent VARCHAR(190) DEFAULT NULL, structure_type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:StructureType)\', label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_90006568E7B003E9 (study_id), INDEX IDX_900065683D8E604F (parent), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution_contents (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, access ENUM(\'1\', \'2\', \'3\') NOT NULL COMMENT \'(DC2Type:DistributionAccessType)\', is_published TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_91757299A4483781 (distribution), INDEX IDX_91757299DE12AB56 (created_by), INDEX IDX_9175729916FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_992ABE46DE12AB56 (created_by), INDEX IDX_992ABE4616FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_module (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, title VARCHAR(255) NOT NULL, `order` INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_B9356A66992ABE46 (data_model), INDEX IDX_B9356A66DE12AB56 (created_by), INDEX IDX_B9356A6616FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_prefix (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, prefix VARCHAR(255) NOT NULL, uri VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_26A0CAC0992ABE46 (data_model), INDEX IDX_26A0CAC0DE12AB56 (created_by), INDEX IDX_26A0CAC016FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_node (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_671DFE7B992ABE46 (data_model), INDEX IDX_671DFE7BDE12AB56 (created_by), INDEX IDX_671DFE7B16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_node_external (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', iri VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_node_internal (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_node_literal (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', value VARCHAR(255) NOT NULL, data_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_node_record (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_node_value (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', is_annotated_value TINYINT(1) NOT NULL, data_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_predicate (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, iri VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_7C632AFF992ABE46 (data_model), INDEX IDX_7C632AFFDE12AB56 (created_by), INDEX IDX_7C632AFF16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_triple (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', module CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', subject CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', predicate CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', object CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_F13D7030C242628 (module), INDEX IDX_F13D7030FBCE3E7A (subject), INDEX IDX_F13D7030301BAA7B (predicate), INDEX IDX_F13D7030A8ADABEC (object), INDEX IDX_F13D7030DE12AB56 (created_by), INDEX IDX_F13D703016FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution_rdf_mappings (distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', node CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', entity VARCHAR(190) NOT NULL, created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_DA9D49EFA4483781 (distribution), INDEX IDX_DA9D49EF857FE845 (node), INDEX IDX_DA9D49EFE284468 (entity), INDEX IDX_DA9D49EFDE12AB56 (created_by), INDEX IDX_DA9D49EF16FE72E1 (updated_by), PRIMARY KEY(distribution, node, entity)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE annotation (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', entity VARCHAR(190) NOT NULL, concept CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_2E443EF2E284468 (entity), INDEX IDX_2E443EF2E74A6050 (concept), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ontology (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', url VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', name VARCHAR(255) NOT NULL, bio_portal_id VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ontology_concept (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ontology CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', url VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', code VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, INDEX IDX_5972B8FFDAF05D3 (ontology), INDEX ontology_code (ontology, code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution_databases (distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', database_name VARCHAR(255) NOT NULL, user TEXT NOT NULL, password TEXT NOT NULL, PRIMARY KEY(distribution)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE castor_entity ADD CONSTRAINT FK_90006568E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id)');
        $this->addSql('ALTER TABLE castor_entity ADD CONSTRAINT FK_900065683D8E604F FOREIGN KEY (parent) REFERENCES castor_entity (id)');
        $this->addSql('ALTER TABLE distribution_contents ADD CONSTRAINT FK_91757299A4483781 FOREIGN KEY (distribution) REFERENCES distribution (id)');
        $this->addSql('ALTER TABLE distribution_contents ADD CONSTRAINT FK_91757299DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE distribution_contents ADD CONSTRAINT FK_9175729916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model ADD CONSTRAINT FK_992ABE46DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model ADD CONSTRAINT FK_992ABE4616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A66992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id)');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A66DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A6616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_prefix ADD CONSTRAINT FK_26A0CAC0992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id)');
        $this->addSql('ALTER TABLE data_model_prefix ADD CONSTRAINT FK_26A0CAC0DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_prefix ADD CONSTRAINT FK_26A0CAC016FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_node ADD CONSTRAINT FK_671DFE7B992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id)');
        $this->addSql('ALTER TABLE data_model_node ADD CONSTRAINT FK_671DFE7BDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_node ADD CONSTRAINT FK_671DFE7B16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_node_external ADD CONSTRAINT FK_FD7D7D44BF396750 FOREIGN KEY (id) REFERENCES data_model_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_internal ADD CONSTRAINT FK_AEDCC1FFBF396750 FOREIGN KEY (id) REFERENCES data_model_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_literal ADD CONSTRAINT FK_B4D5BACFBF396750 FOREIGN KEY (id) REFERENCES data_model_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_record ADD CONSTRAINT FK_9D711823BF396750 FOREIGN KEY (id) REFERENCES data_model_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_value ADD CONSTRAINT FK_EE45F571BF396750 FOREIGN KEY (id) REFERENCES data_model_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_predicate ADD CONSTRAINT FK_7C632AFF992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id)');
        $this->addSql('ALTER TABLE data_model_predicate ADD CONSTRAINT FK_7C632AFFDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_predicate ADD CONSTRAINT FK_7C632AFF16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_triple ADD CONSTRAINT FK_F13D7030C242628 FOREIGN KEY (module) REFERENCES data_model_module (id)');
        $this->addSql('ALTER TABLE data_model_triple ADD CONSTRAINT FK_F13D7030FBCE3E7A FOREIGN KEY (subject) REFERENCES data_model_node (id)');
        $this->addSql('ALTER TABLE data_model_triple ADD CONSTRAINT FK_F13D7030301BAA7B FOREIGN KEY (predicate) REFERENCES data_model_predicate (id)');
        $this->addSql('ALTER TABLE data_model_triple ADD CONSTRAINT FK_F13D7030A8ADABEC FOREIGN KEY (object) REFERENCES data_model_node (id)');
        $this->addSql('ALTER TABLE data_model_triple ADD CONSTRAINT FK_F13D7030DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_triple ADD CONSTRAINT FK_F13D703016FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD CONSTRAINT FK_DA9D49EFA4483781 FOREIGN KEY (distribution) REFERENCES distribution_rdf (id)');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD CONSTRAINT FK_DA9D49EF857FE845 FOREIGN KEY (node) REFERENCES data_model_node (id)');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD CONSTRAINT FK_DA9D49EFE284468 FOREIGN KEY (entity) REFERENCES castor_entity (id)');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD CONSTRAINT FK_DA9D49EFDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD CONSTRAINT FK_DA9D49EF16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE annotation ADD CONSTRAINT FK_2E443EF2E284468 FOREIGN KEY (entity) REFERENCES castor_entity (id)');
        $this->addSql('ALTER TABLE annotation ADD CONSTRAINT FK_2E443EF2E74A6050 FOREIGN KEY (concept) REFERENCES ontology_concept (id)');
        $this->addSql('ALTER TABLE ontology_concept ADD CONSTRAINT FK_5972B8FFDAF05D3 FOREIGN KEY (ontology) REFERENCES ontology (id)');
        $this->addSql('ALTER TABLE distribution_databases ADD CONSTRAINT FK_8086D1DEA4483781 FOREIGN KEY (distribution) REFERENCES distribution (id)');
        $this->addSql('DROP TABLE distribution_rdf_modules');
        $this->addSql('DROP INDEX slug ON study');
        $this->addSql('CREATE INDEX slug ON study (slug)');
        $this->addSql('ALTER TABLE distribution_csv DROP FOREIGN KEY FK_D815CB1ABF396750');
        $this->addSql('ALTER TABLE distribution_csv ADD CONSTRAINT FK_D815CB1ABF396750 FOREIGN KEY (id) REFERENCES distribution_contents (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_csv_elements DROP FOREIGN KEY FK_BF23FC986EB6DDB5');
        $this->addSql('DROP INDEX IDX_BF23FC986EB6DDB5 ON distribution_csv_elements');
        $this->addSql('ALTER TABLE distribution_csv_elements CHANGE distribution_id distribution CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_csv_elements ADD CONSTRAINT FK_BF23FC98A4483781 FOREIGN KEY (distribution) REFERENCES distribution_csv (id)');
        $this->addSql('CREATE INDEX IDX_BF23FC98A4483781 ON distribution_csv_elements (distribution)');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AFBF396750');
        $this->addSql('ALTER TABLE distribution_rdf ADD data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', DROP prefix');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AF992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id)');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AFBF396750 FOREIGN KEY (id) REFERENCES distribution_contents (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_DDC596AF992ABE46 ON distribution_rdf (data_model)');
        $this->addSql('DROP INDEX slug ON agent');
        $this->addSql('CREATE INDEX slug ON agent (slug)');
        $this->addSql('DROP INDEX slug ON catalog');
        $this->addSql('ALTER TABLE catalog CHANGE created created DATETIME NOT NULL');
        $this->addSql('CREATE INDEX slug ON catalog (slug)');
        $this->addSql('DROP INDEX slug ON dataset');
        $this->addSql('ALTER TABLE dataset CHANGE created created DATETIME NOT NULL');
        $this->addSql('CREATE INDEX slug ON dataset (slug)');
        $this->addSql('DROP INDEX slug ON distribution');
        $this->addSql('ALTER TABLE distribution DROP access, DROP dtype, DROP is_published, CHANGE created created DATETIME NOT NULL');
        $this->addSql('CREATE INDEX slug ON distribution (slug)');
        $this->addSql('DROP INDEX iri ON fdp');
        $this->addSql('CREATE INDEX iri ON fdp (iri)');
        $this->addSql('ALTER TABLE user_api CHANGE client_id client_id TEXT NOT NULL, CHANGE client_secret client_secret TEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE castor_entity DROP FOREIGN KEY FK_900065683D8E604F');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP FOREIGN KEY FK_DA9D49EFE284468');
        $this->addSql('ALTER TABLE annotation DROP FOREIGN KEY FK_2E443EF2E284468');
        $this->addSql('ALTER TABLE distribution_csv DROP FOREIGN KEY FK_D815CB1ABF396750');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AFBF396750');
        $this->addSql('ALTER TABLE data_model_module DROP FOREIGN KEY FK_B9356A66992ABE46');
        $this->addSql('ALTER TABLE data_model_prefix DROP FOREIGN KEY FK_26A0CAC0992ABE46');
        $this->addSql('ALTER TABLE data_model_node DROP FOREIGN KEY FK_671DFE7B992ABE46');
        $this->addSql('ALTER TABLE data_model_predicate DROP FOREIGN KEY FK_7C632AFF992ABE46');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AF992ABE46');
        $this->addSql('ALTER TABLE data_model_triple DROP FOREIGN KEY FK_F13D7030C242628');
        $this->addSql('ALTER TABLE data_model_node_external DROP FOREIGN KEY FK_FD7D7D44BF396750');
        $this->addSql('ALTER TABLE data_model_node_internal DROP FOREIGN KEY FK_AEDCC1FFBF396750');
        $this->addSql('ALTER TABLE data_model_node_literal DROP FOREIGN KEY FK_B4D5BACFBF396750');
        $this->addSql('ALTER TABLE data_model_node_record DROP FOREIGN KEY FK_9D711823BF396750');
        $this->addSql('ALTER TABLE data_model_node_value DROP FOREIGN KEY FK_EE45F571BF396750');
        $this->addSql('ALTER TABLE data_model_triple DROP FOREIGN KEY FK_F13D7030FBCE3E7A');
        $this->addSql('ALTER TABLE data_model_triple DROP FOREIGN KEY FK_F13D7030A8ADABEC');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP FOREIGN KEY FK_DA9D49EF857FE845');
        $this->addSql('ALTER TABLE data_model_triple DROP FOREIGN KEY FK_F13D7030301BAA7B');
        $this->addSql('ALTER TABLE ontology_concept DROP FOREIGN KEY FK_5972B8FFDAF05D3');
        $this->addSql('ALTER TABLE annotation DROP FOREIGN KEY FK_2E443EF2E74A6050');
        $this->addSql('CREATE TABLE distribution_rdf_modules (id CHAR(36) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', distribution_id CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', title VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, `order` INT NOT NULL, twig LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, INDEX IDX_95CA20636EB6DDB5 (distribution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE distribution_rdf_modules ADD CONSTRAINT FK_95CA20636EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES distribution_rdf (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE castor_entity');
        $this->addSql('DROP TABLE distribution_contents');
        $this->addSql('DROP TABLE data_model');
        $this->addSql('DROP TABLE data_model_module');
        $this->addSql('DROP TABLE data_model_prefix');
        $this->addSql('DROP TABLE data_model_node');
        $this->addSql('DROP TABLE data_model_node_external');
        $this->addSql('DROP TABLE data_model_node_internal');
        $this->addSql('DROP TABLE data_model_node_literal');
        $this->addSql('DROP TABLE data_model_node_record');
        $this->addSql('DROP TABLE data_model_node_value');
        $this->addSql('DROP TABLE data_model_predicate');
        $this->addSql('DROP TABLE data_model_triple');
        $this->addSql('DROP TABLE distribution_rdf_mappings');
        $this->addSql('DROP TABLE annotation');
        $this->addSql('DROP TABLE ontology');
        $this->addSql('DROP TABLE ontology_concept');
        $this->addSql('DROP TABLE distribution_databases');
        $this->addSql('DROP INDEX slug ON agent');
        $this->addSql('CREATE INDEX slug ON agent (slug(191))');
        $this->addSql('DROP INDEX slug ON catalog');
        $this->addSql('ALTER TABLE catalog CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX slug ON catalog (slug(191))');
        $this->addSql('DROP INDEX slug ON dataset');
        $this->addSql('ALTER TABLE dataset CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX slug ON dataset (slug(191))');
        $this->addSql('DROP INDEX slug ON distribution');
        $this->addSql('ALTER TABLE distribution ADD access ENUM(\'1\', \'2\', \'3\') CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:DistributionAccessType)\', ADD dtype VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, ADD is_published TINYINT(1) NOT NULL, CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX slug ON distribution (slug(191))');
        $this->addSql('ALTER TABLE distribution_csv DROP FOREIGN KEY FK_D815CB1ABF396750');
        $this->addSql('ALTER TABLE distribution_csv ADD CONSTRAINT FK_D815CB1ABF396750 FOREIGN KEY (id) REFERENCES distribution (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_csv_elements DROP FOREIGN KEY FK_BF23FC98A4483781');
        $this->addSql('DROP INDEX IDX_BF23FC98A4483781 ON distribution_csv_elements');
        $this->addSql('ALTER TABLE distribution_csv_elements CHANGE distribution distribution_id CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_csv_elements ADD CONSTRAINT FK_BF23FC986EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES distribution_csv (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_BF23FC986EB6DDB5 ON distribution_csv_elements (distribution_id)');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AFBF396750');
        $this->addSql('DROP INDEX IDX_DDC596AF992ABE46 ON distribution_rdf');
        $this->addSql('ALTER TABLE distribution_rdf ADD prefix LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, DROP data_model');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AFBF396750 FOREIGN KEY (id) REFERENCES distribution (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP INDEX iri ON fdp');
        $this->addSql('CREATE INDEX iri ON fdp (iri(191))');
        $this->addSql('DROP INDEX slug ON study');
        $this->addSql('CREATE INDEX slug ON study (slug(191))');
        $this->addSql('ALTER TABLE user_api CHANGE client_id client_id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE client_secret client_secret VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
    }
}
