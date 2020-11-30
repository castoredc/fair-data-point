<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function sprintf;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201118124315 extends AbstractMigration
{
    public function preUp(Schema $schema): void
    {
        parent::preUp($schema);
        $this->setForeignKeyChecks(false);
    }

    public function postUp(Schema $schema): void
    {
        parent::postUp($schema);
        $this->setForeignKeyChecks(true);
    }

    public function preDown(Schema $schema): void
    {
        parent::preDown($schema);
        $this->setForeignKeyChecks(false);
    }

    public function postDown(Schema $schema): void
    {
        parent::postDown($schema);
        $this->setForeignKeyChecks(true);
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_dictionary_dependency_group DROP FOREIGN KEY FK_4C41AB91BF396750');
        $this->addSql('ALTER TABLE data_dictionary_dependency_rule DROP FOREIGN KEY FK_73C34DF8BF396750');
        $this->addSql('ALTER TABLE data_dictionary_dependency DROP FOREIGN KEY FK_7E2D937EFE54D947');
        $this->addSql('ALTER TABLE data_dictionary_group DROP FOREIGN KEY FK_F8ECFBBEA0F708D');
        $this->addSql('ALTER TABLE data_model_dependency_group DROP FOREIGN KEY FK_E7C973BF396750');
        $this->addSql('ALTER TABLE data_model_dependency_rule DROP FOREIGN KEY FK_BCB1F1D9BF396750');
        $this->addSql('ALTER TABLE data_model_dependency DROP FOREIGN KEY FK_C5C16ABCFE54D947');
        $this->addSql('ALTER TABLE data_model_module DROP FOREIGN KEY FK_B9356A66EA0F708D');
        $this->addSql('CREATE TABLE data_specification (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_FF7D0EB0DE12AB56 (created_by), INDEX IDX_FF7D0EB016FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_specification_dependency (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_2AF6C929FE54D947 (group_id), INDEX IDX_2AF6C929DE12AB56 (created_by), INDEX IDX_2AF6C92916FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_specification_dependency_group (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', combinator VARCHAR(255) NOT NULL COMMENT \'(DC2Type:DependencyCombinatorType)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_specification_dependency_rule (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', element CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', operator VARCHAR(255) NOT NULL COMMENT \'(DC2Type:DependencyOperatorType)\', value VARCHAR(255) NOT NULL, INDEX IDX_24BF36441405E39 (element), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_specification_element (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', `group` CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, `order` INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_CE19CB2D6DC044C5 (`group`), INDEX IDX_CE19CB2DBF1CD3C3 (version), INDEX IDX_CE19CB2DDE12AB56 (created_by), INDEX IDX_CE19CB2D16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_specification_elementgroup (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', `group` CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_B353A4486DC044C5 (`group`), INDEX IDX_B353A448DE12AB56 (created_by), INDEX IDX_B353A44816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_specification_group (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dependencies CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, `order` INT NOT NULL, is_repeated TINYINT(1) DEFAULT \'0\' NOT NULL, is_dependent TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_65CF49E1BF1CD3C3 (version), UNIQUE INDEX UNIQ_65CF49E1EA0F708D (dependencies), INDEX IDX_65CF49E1DE12AB56 (created_by), INDEX IDX_65CF49E116FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_specification_mappings (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', study CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', entity VARCHAR(190) NOT NULL, version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', element CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', `group` CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_C7FCEF1E67F9749 (study), INDEX IDX_C7FCEF1E284468 (entity), INDEX IDX_C7FCEF1BF1CD3C3 (version), INDEX IDX_C7FCEF1DE12AB56 (created_by), INDEX IDX_C7FCEF116FE72E1 (updated_by), INDEX IDX_C7FCEF141405E39 (element), INDEX IDX_C7FCEF16DC044C5 (`group`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_specification_version (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_specification CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', version VARCHAR(255) NOT NULL COMMENT \'(DC2Type:version)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_304546D7FF7D0EB0 (data_specification), INDEX IDX_304546D7DE12AB56 (created_by), INDEX IDX_304546D716FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE data_specification ADD CONSTRAINT FK_FF7D0EB0DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification ADD CONSTRAINT FK_FF7D0EB016FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_dependency ADD CONSTRAINT FK_2AF6C929FE54D947 FOREIGN KEY (group_id) REFERENCES data_specification_dependency_group (id)');
        $this->addSql('ALTER TABLE data_specification_dependency ADD CONSTRAINT FK_2AF6C929DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_dependency ADD CONSTRAINT FK_2AF6C92916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_dependency_group ADD CONSTRAINT FK_B589FC40BF396750 FOREIGN KEY (id) REFERENCES data_specification_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_specification_dependency_rule ADD CONSTRAINT FK_24BF36441405E39 FOREIGN KEY (element) REFERENCES data_specification_element (id)');
        $this->addSql('ALTER TABLE data_specification_dependency_rule ADD CONSTRAINT FK_24BF364BF396750 FOREIGN KEY (id) REFERENCES data_specification_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_specification_element ADD CONSTRAINT FK_CE19CB2D6DC044C5 FOREIGN KEY (`group`) REFERENCES data_specification_group (id)');
        $this->addSql('ALTER TABLE data_specification_element ADD CONSTRAINT FK_CE19CB2DBF1CD3C3 FOREIGN KEY (version) REFERENCES data_specification_version (id)');
        $this->addSql('ALTER TABLE data_specification_element ADD CONSTRAINT FK_CE19CB2DDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_element ADD CONSTRAINT FK_CE19CB2D16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_elementgroup ADD CONSTRAINT FK_B353A4486DC044C5 FOREIGN KEY (`group`) REFERENCES data_specification_group (id)');
        $this->addSql('ALTER TABLE data_specification_elementgroup ADD CONSTRAINT FK_B353A448DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_elementgroup ADD CONSTRAINT FK_B353A44816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_group ADD CONSTRAINT FK_65CF49E1BF1CD3C3 FOREIGN KEY (version) REFERENCES data_specification_version (id)');
        $this->addSql('ALTER TABLE data_specification_group ADD CONSTRAINT FK_65CF49E1EA0F708D FOREIGN KEY (dependencies) REFERENCES data_specification_dependency_group (id)');
        $this->addSql('ALTER TABLE data_specification_group ADD CONSTRAINT FK_65CF49E1DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_group ADD CONSTRAINT FK_65CF49E116FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF1E67F9749 FOREIGN KEY (study) REFERENCES study (id)');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF1E284468 FOREIGN KEY (entity) REFERENCES castor_entity (id)');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF1BF1CD3C3 FOREIGN KEY (version) REFERENCES data_specification_version (id)');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF1DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF116FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF141405E39 FOREIGN KEY (element) REFERENCES data_specification_element (id)');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF16DC044C5 FOREIGN KEY (`group`) REFERENCES data_specification_group (id)');
        $this->addSql('ALTER TABLE data_specification_version ADD CONSTRAINT FK_304546D7FF7D0EB0 FOREIGN KEY (data_specification) REFERENCES data_specification (id)');
        $this->addSql('ALTER TABLE data_specification_version ADD CONSTRAINT FK_304546D7DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_specification_version ADD CONSTRAINT FK_304546D716FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('DROP TABLE data_dictionary_dependency');
        $this->addSql('DROP TABLE data_dictionary_dependency_group');
        $this->addSql('DROP TABLE data_dictionary_dependency_rule');
        $this->addSql('DROP TABLE data_model_dependency');
        $this->addSql('DROP TABLE data_model_dependency_group');
        $this->addSql('DROP TABLE data_model_dependency_rule');
        $this->addSql('DROP TABLE data_model_mappings');
        $this->addSql('ALTER TABLE data_dictionary DROP FOREIGN KEY FK_D095DFFF16FE72E1');
        $this->addSql('ALTER TABLE data_dictionary DROP FOREIGN KEY FK_D095DFFFDE12AB56');
        $this->addSql('DROP INDEX IDX_D095DFFF16FE72E1 ON data_dictionary');
        $this->addSql('DROP INDEX IDX_D095DFFFDE12AB56 ON data_dictionary');
        $this->addSql('ALTER TABLE data_dictionary DROP created_by, DROP updated_by, DROP title, DROP description, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE data_dictionary ADD CONSTRAINT FK_D095DFFFBF396750 FOREIGN KEY (id) REFERENCES data_specification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_dictionary_group DROP FOREIGN KEY FK_F8ECFBB16FE72E1');
        $this->addSql('ALTER TABLE data_dictionary_group DROP FOREIGN KEY FK_F8ECFBB74CABF5D');
        $this->addSql('ALTER TABLE data_dictionary_group DROP FOREIGN KEY FK_F8ECFBBDE12AB56');
        $this->addSql('DROP INDEX IDX_F8ECFBB74CABF5D ON data_dictionary_group');
        $this->addSql('DROP INDEX IDX_F8ECFBB16FE72E1 ON data_dictionary_group');
        $this->addSql('DROP INDEX UNIQ_F8ECFBBEA0F708D ON data_dictionary_group');
        $this->addSql('DROP INDEX IDX_F8ECFBBDE12AB56 ON data_dictionary_group');
        $this->addSql('ALTER TABLE data_dictionary_group DROP data_dictionary_version, DROP dependencies, DROP created_by, DROP updated_by, DROP title, DROP `order`, DROP is_repeated, DROP is_dependent, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE data_dictionary_group ADD CONSTRAINT FK_F8ECFBBBF396750 FOREIGN KEY (id) REFERENCES data_specification_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_dictionary_variable DROP FOREIGN KEY FK_DB31EFA216FE72E1');
        $this->addSql('ALTER TABLE data_dictionary_variable DROP FOREIGN KEY FK_DB31EFA26DC044C5');
        $this->addSql('ALTER TABLE data_dictionary_variable DROP FOREIGN KEY FK_DB31EFA2DE12AB56');
        $this->addSql('DROP INDEX IDX_DB31EFA216FE72E1 ON data_dictionary_variable');
        $this->addSql('DROP INDEX IDX_DB31EFA26DC044C5 ON data_dictionary_variable');
        $this->addSql('DROP INDEX IDX_DB31EFA2DE12AB56 ON data_dictionary_variable');
        $this->addSql('ALTER TABLE data_dictionary_variable DROP `group`, DROP created_by, DROP updated_by, DROP label, DROP description, DROP `order`, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE data_dictionary_variable ADD CONSTRAINT FK_DB31EFA2BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_dictionary_version DROP FOREIGN KEY FK_74CABF5D16FE72E1');
        $this->addSql('ALTER TABLE data_dictionary_version DROP FOREIGN KEY FK_74CABF5DD095DFFF');
        $this->addSql('ALTER TABLE data_dictionary_version DROP FOREIGN KEY FK_74CABF5DDE12AB56');
        $this->addSql('DROP INDEX IDX_74CABF5DDE12AB56 ON data_dictionary_version');
        $this->addSql('DROP INDEX IDX_74CABF5DD095DFFF ON data_dictionary_version');
        $this->addSql('DROP INDEX IDX_74CABF5D16FE72E1 ON data_dictionary_version');
        $this->addSql('ALTER TABLE data_dictionary_version DROP data_dictionary, DROP created_by, DROP updated_by, DROP version, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE data_dictionary_version ADD CONSTRAINT FK_74CABF5DBF396750 FOREIGN KEY (id) REFERENCES data_specification_version (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model DROP FOREIGN KEY FK_992ABE4616FE72E1');
        $this->addSql('ALTER TABLE data_model DROP FOREIGN KEY FK_992ABE46DE12AB56');
        $this->addSql('DROP INDEX IDX_992ABE4616FE72E1 ON data_model');
        $this->addSql('DROP INDEX IDX_992ABE46DE12AB56 ON data_model');
        $this->addSql('ALTER TABLE data_model DROP created_by, DROP updated_by, DROP title, DROP description, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE data_model ADD CONSTRAINT FK_992ABE46BF396750 FOREIGN KEY (id) REFERENCES data_specification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_module DROP FOREIGN KEY FK_B9356A6616FE72E1');
        $this->addSql('ALTER TABLE data_model_module DROP FOREIGN KEY FK_B9356A66992ABE46');
        $this->addSql('ALTER TABLE data_model_module DROP FOREIGN KEY FK_B9356A66DE12AB56');
        $this->addSql('DROP INDEX IDX_B9356A66992ABE46 ON data_model_module');
        $this->addSql('DROP INDEX IDX_B9356A6616FE72E1 ON data_model_module');
        $this->addSql('DROP INDEX UNIQ_B9356A66EA0F708D ON data_model_module');
        $this->addSql('DROP INDEX IDX_B9356A66DE12AB56 ON data_model_module');
        $this->addSql('ALTER TABLE data_model_module DROP data_model, DROP created_by, DROP updated_by, DROP dependencies, DROP title, DROP `order`, DROP created_at, DROP updated_at, DROP is_repeated, DROP is_dependent');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A66BF396750 FOREIGN KEY (id) REFERENCES data_specification_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node DROP FOREIGN KEY FK_671DFE7B16FE72E1');
        $this->addSql('ALTER TABLE data_model_node DROP FOREIGN KEY FK_671DFE7B992ABE46');
        $this->addSql('ALTER TABLE data_model_node DROP FOREIGN KEY FK_671DFE7BDE12AB56');
        $this->addSql('DROP INDEX IDX_671DFE7BDE12AB56 ON data_model_node');
        $this->addSql('DROP INDEX IDX_671DFE7B992ABE46 ON data_model_node');
        $this->addSql('DROP INDEX IDX_671DFE7B16FE72E1 ON data_model_node');
        $this->addSql('ALTER TABLE data_model_node DROP data_model, DROP created_by, DROP updated_by, DROP title, DROP description, DROP created_at, DROP updated_at, DROP dtype');
        $this->addSql('ALTER TABLE data_model_node ADD CONSTRAINT FK_671DFE7BBF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_external DROP FOREIGN KEY FK_FD7D7D44BF396750');
        $this->addSql('ALTER TABLE data_model_node_external ADD CONSTRAINT FK_FD7D7D44BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_internal DROP FOREIGN KEY FK_AEDCC1FFBF396750');
        $this->addSql('ALTER TABLE data_model_node_internal ADD CONSTRAINT FK_AEDCC1FFBF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_literal DROP FOREIGN KEY FK_B4D5BACFBF396750');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_literal ADD CONSTRAINT FK_B4D5BACFBF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_record DROP FOREIGN KEY FK_9D711823BF396750');
        $this->addSql('ALTER TABLE data_model_node_record ADD CONSTRAINT FK_9D711823BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_value DROP FOREIGN KEY FK_EE45F571BF396750');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value ADD CONSTRAINT FK_EE45F571BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_triple DROP FOREIGN KEY FK_F13D703016FE72E1');
        $this->addSql('ALTER TABLE data_model_triple DROP FOREIGN KEY FK_F13D7030C242628');
        $this->addSql('ALTER TABLE data_model_triple DROP FOREIGN KEY FK_F13D7030DE12AB56');
        $this->addSql('DROP INDEX IDX_F13D703016FE72E1 ON data_model_triple');
        $this->addSql('DROP INDEX IDX_F13D7030DE12AB56 ON data_model_triple');
        $this->addSql('DROP INDEX IDX_F13D7030C242628 ON data_model_triple');
        $this->addSql('ALTER TABLE data_model_triple DROP module, DROP created_by, DROP updated_by, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE data_model_triple ADD CONSTRAINT FK_F13D7030BF396750 FOREIGN KEY (id) REFERENCES data_specification_elementgroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_version DROP FOREIGN KEY FK_2ECDAE1816FE72E1');
        $this->addSql('ALTER TABLE data_model_version DROP FOREIGN KEY FK_2ECDAE18992ABE46');
        $this->addSql('ALTER TABLE data_model_version DROP FOREIGN KEY FK_2ECDAE18DE12AB56');
        $this->addSql('DROP INDEX IDX_2ECDAE18DE12AB56 ON data_model_version');
        $this->addSql('DROP INDEX IDX_2ECDAE18992ABE46 ON data_model_version');
        $this->addSql('DROP INDEX IDX_2ECDAE1816FE72E1 ON data_model_version');
        $this->addSql('ALTER TABLE data_model_version DROP data_model, DROP created_by, DROP updated_by, DROP version, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE data_model_version ADD CONSTRAINT FK_2ECDAE18BF396750 FOREIGN KEY (id) REFERENCES data_specification_version (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_contents ADD data_specification CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD data_specification_version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_contents ADD CONSTRAINT FK_91757299FF7D0EB0 FOREIGN KEY (data_specification) REFERENCES data_specification (id)');
        $this->addSql('ALTER TABLE distribution_contents ADD CONSTRAINT FK_91757299304546D7 FOREIGN KEY (data_specification_version) REFERENCES data_specification_version (id)');
        $this->addSql('CREATE INDEX IDX_91757299FF7D0EB0 ON distribution_contents (data_specification)');
        $this->addSql('CREATE INDEX IDX_91757299304546D7 ON distribution_contents (data_specification_version)');
        $this->addSql('ALTER TABLE distribution_csv DROP FOREIGN KEY FK_D815CB1A74CABF5D');
        $this->addSql('ALTER TABLE distribution_csv DROP FOREIGN KEY FK_D815CB1AD095DFFF');
        $this->addSql('DROP INDEX IDX_D815CB1A74CABF5D ON distribution_csv');
        $this->addSql('DROP INDEX IDX_D815CB1AD095DFFF ON distribution_csv');
        $this->addSql('ALTER TABLE distribution_csv DROP data_dictionary, DROP data_dictionary_version');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AF2ECDAE18');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AF992ABE46');
        $this->addSql('DROP INDEX IDX_DDC596AF2ECDAE18 ON distribution_rdf');
        $this->addSql('DROP INDEX IDX_DDC596AF992ABE46 ON distribution_rdf');
        $this->addSql('ALTER TABLE distribution_rdf DROP data_model, DROP data_model_version');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_dictionary DROP FOREIGN KEY FK_D095DFFFBF396750');
        $this->addSql('ALTER TABLE data_model DROP FOREIGN KEY FK_992ABE46BF396750');
        $this->addSql('ALTER TABLE data_specification_version DROP FOREIGN KEY FK_304546D7FF7D0EB0');
        $this->addSql('ALTER TABLE distribution_contents DROP FOREIGN KEY FK_91757299FF7D0EB0');
        $this->addSql('ALTER TABLE data_specification_dependency_group DROP FOREIGN KEY FK_B589FC40BF396750');
        $this->addSql('ALTER TABLE data_specification_dependency_rule DROP FOREIGN KEY FK_24BF364BF396750');
        $this->addSql('ALTER TABLE data_specification_dependency DROP FOREIGN KEY FK_2AF6C929FE54D947');
        $this->addSql('ALTER TABLE data_specification_group DROP FOREIGN KEY FK_65CF49E1EA0F708D');
        $this->addSql('ALTER TABLE data_dictionary_variable DROP FOREIGN KEY FK_DB31EFA2BF396750');
        $this->addSql('ALTER TABLE data_model_node DROP FOREIGN KEY FK_671DFE7BBF396750');
        $this->addSql('ALTER TABLE data_model_node_external DROP FOREIGN KEY FK_FD7D7D44BF396750');
        $this->addSql('ALTER TABLE data_model_node_internal DROP FOREIGN KEY FK_AEDCC1FFBF396750');
        $this->addSql('ALTER TABLE data_model_node_literal DROP FOREIGN KEY FK_B4D5BACFBF396750');
        $this->addSql('ALTER TABLE data_model_node_record DROP FOREIGN KEY FK_9D711823BF396750');
        $this->addSql('ALTER TABLE data_model_node_value DROP FOREIGN KEY FK_EE45F571BF396750');
        $this->addSql('ALTER TABLE data_specification_dependency_rule DROP FOREIGN KEY FK_24BF36441405E39');
        $this->addSql('ALTER TABLE data_specification_mappings DROP FOREIGN KEY FK_C7FCEF141405E39');
        $this->addSql('ALTER TABLE data_model_triple DROP FOREIGN KEY FK_F13D7030BF396750');
        $this->addSql('ALTER TABLE data_dictionary_group DROP FOREIGN KEY FK_F8ECFBBBF396750');
        $this->addSql('ALTER TABLE data_model_module DROP FOREIGN KEY FK_B9356A66BF396750');
        $this->addSql('ALTER TABLE data_specification_element DROP FOREIGN KEY FK_CE19CB2D6DC044C5');
        $this->addSql('ALTER TABLE data_specification_elementgroup DROP FOREIGN KEY FK_B353A4486DC044C5');
        $this->addSql('ALTER TABLE data_specification_mappings DROP FOREIGN KEY FK_C7FCEF16DC044C5');
        $this->addSql('ALTER TABLE data_dictionary_version DROP FOREIGN KEY FK_74CABF5DBF396750');
        $this->addSql('ALTER TABLE data_model_version DROP FOREIGN KEY FK_2ECDAE18BF396750');
        $this->addSql('ALTER TABLE data_specification_element DROP FOREIGN KEY FK_CE19CB2DBF1CD3C3');
        $this->addSql('ALTER TABLE data_specification_group DROP FOREIGN KEY FK_65CF49E1BF1CD3C3');
        $this->addSql('ALTER TABLE data_specification_mappings DROP FOREIGN KEY FK_C7FCEF1BF1CD3C3');
        $this->addSql('ALTER TABLE distribution_contents DROP FOREIGN KEY FK_91757299304546D7');
        $this->addSql('CREATE TABLE data_dictionary_dependency (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', group_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_7E2D937EDE12AB56 (created_by), INDEX IDX_7E2D937EFE54D947 (group_id), INDEX IDX_7E2D937E16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE data_dictionary_dependency_group (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', combinator VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:DependencyCombinatorType)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE data_dictionary_dependency_rule (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', variable CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', operator VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:DependencyOperatorType)\', value VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_73C34DF8CC4D878D (variable), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE data_model_dependency (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', group_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_C5C16ABCDE12AB56 (created_by), INDEX IDX_C5C16ABCFE54D947 (group_id), INDEX IDX_C5C16ABC16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE data_model_dependency_group (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', combinator VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:DependencyCombinatorType)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE data_model_dependency_rule (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', node CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', operator VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:DependencyOperatorType)\', value VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_BCB1F1D9857FE845 (node), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE data_model_mappings (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', node CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', entity VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', data_model_version CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', module CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', study CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_7BA9938C242628 (module), INDEX IDX_7BA9938E284468 (entity), INDEX IDX_7BA993816FE72E1 (updated_by), INDEX IDX_7BA99382ECDAE18 (data_model_version), INDEX IDX_7BA9938E67F9749 (study), INDEX IDX_7BA9938857FE845 (node), INDEX IDX_7BA9938DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE data_dictionary_dependency ADD CONSTRAINT FK_7E2D937E16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_dependency ADD CONSTRAINT FK_7E2D937EDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_dependency ADD CONSTRAINT FK_7E2D937EFE54D947 FOREIGN KEY (group_id) REFERENCES data_dictionary_dependency_group (id)');
        $this->addSql('ALTER TABLE data_dictionary_dependency_group ADD CONSTRAINT FK_4C41AB91BF396750 FOREIGN KEY (id) REFERENCES data_dictionary_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_dictionary_dependency_rule ADD CONSTRAINT FK_73C34DF8BF396750 FOREIGN KEY (id) REFERENCES data_dictionary_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_dictionary_dependency_rule ADD CONSTRAINT FK_73C34DF8CC4D878D FOREIGN KEY (variable) REFERENCES data_dictionary_variable (id)');
        $this->addSql('ALTER TABLE data_model_dependency ADD CONSTRAINT FK_C5C16ABC16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_dependency ADD CONSTRAINT FK_C5C16ABCDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_dependency ADD CONSTRAINT FK_C5C16ABCFE54D947 FOREIGN KEY (group_id) REFERENCES data_model_dependency_group (id)');
        $this->addSql('ALTER TABLE data_model_dependency_group ADD CONSTRAINT FK_E7C973BF396750 FOREIGN KEY (id) REFERENCES data_model_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_dependency_rule ADD CONSTRAINT FK_BCB1F1D9857FE845 FOREIGN KEY (node) REFERENCES data_model_node_value (id)');
        $this->addSql('ALTER TABLE data_model_dependency_rule ADD CONSTRAINT FK_BCB1F1D9BF396750 FOREIGN KEY (id) REFERENCES data_model_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_mappings ADD CONSTRAINT FK_DA9D49EF16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_mappings ADD CONSTRAINT FK_DA9D49EF2ECDAE18 FOREIGN KEY (data_model_version) REFERENCES data_model_version (id)');
        $this->addSql('ALTER TABLE data_model_mappings ADD CONSTRAINT FK_DA9D49EF857FE845 FOREIGN KEY (node) REFERENCES data_model_node (id)');
        $this->addSql('ALTER TABLE data_model_mappings ADD CONSTRAINT FK_DA9D49EFC242628 FOREIGN KEY (module) REFERENCES data_model_module (id)');
        $this->addSql('ALTER TABLE data_model_mappings ADD CONSTRAINT FK_DA9D49EFDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_mappings ADD CONSTRAINT FK_DA9D49EFE284468 FOREIGN KEY (entity) REFERENCES castor_entity (id)');
        $this->addSql('ALTER TABLE data_model_mappings ADD CONSTRAINT FK_DA9D49EFE67F9749 FOREIGN KEY (study) REFERENCES study (id)');
        $this->addSql('DROP TABLE data_specification');
        $this->addSql('DROP TABLE data_specification_dependency');
        $this->addSql('DROP TABLE data_specification_dependency_group');
        $this->addSql('DROP TABLE data_specification_dependency_rule');
        $this->addSql('DROP TABLE data_specification_element');
        $this->addSql('DROP TABLE data_specification_elementgroup');
        $this->addSql('DROP TABLE data_specification_group');
        $this->addSql('DROP TABLE data_specification_mappings');
        $this->addSql('DROP TABLE data_specification_version');
        $this->addSql('ALTER TABLE data_dictionary ADD created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE data_dictionary ADD CONSTRAINT FK_D095DFFF16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary ADD CONSTRAINT FK_D095DFFFDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D095DFFF16FE72E1 ON data_dictionary (updated_by)');
        $this->addSql('CREATE INDEX IDX_D095DFFFDE12AB56 ON data_dictionary (created_by)');
        $this->addSql('ALTER TABLE data_dictionary_group ADD data_dictionary_version CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD dependencies CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD `order` INT NOT NULL, ADD is_repeated TINYINT(1) DEFAULT \'0\' NOT NULL, ADD is_dependent TINYINT(1) DEFAULT \'0\' NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE data_dictionary_group ADD CONSTRAINT FK_F8ECFBB16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_group ADD CONSTRAINT FK_F8ECFBB74CABF5D FOREIGN KEY (data_dictionary_version) REFERENCES data_dictionary_version (id)');
        $this->addSql('ALTER TABLE data_dictionary_group ADD CONSTRAINT FK_F8ECFBBDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_group ADD CONSTRAINT FK_F8ECFBBEA0F708D FOREIGN KEY (dependencies) REFERENCES data_dictionary_dependency_group (id)');
        $this->addSql('CREATE INDEX IDX_F8ECFBB74CABF5D ON data_dictionary_group (data_dictionary_version)');
        $this->addSql('CREATE INDEX IDX_F8ECFBB16FE72E1 ON data_dictionary_group (updated_by)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F8ECFBBEA0F708D ON data_dictionary_group (dependencies)');
        $this->addSql('CREATE INDEX IDX_F8ECFBBDE12AB56 ON data_dictionary_group (created_by)');
        $this->addSql('ALTER TABLE data_dictionary_variable ADD `group` CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD label VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD `order` INT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE data_dictionary_variable ADD CONSTRAINT FK_DB31EFA216FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_variable ADD CONSTRAINT FK_DB31EFA26DC044C5 FOREIGN KEY (`group`) REFERENCES data_dictionary_group (id)');
        $this->addSql('ALTER TABLE data_dictionary_variable ADD CONSTRAINT FK_DB31EFA2DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DB31EFA216FE72E1 ON data_dictionary_variable (updated_by)');
        $this->addSql('CREATE INDEX IDX_DB31EFA26DC044C5 ON data_dictionary_variable (`group`)');
        $this->addSql('CREATE INDEX IDX_DB31EFA2DE12AB56 ON data_dictionary_variable (created_by)');
        $this->addSql('ALTER TABLE data_dictionary_version ADD data_dictionary CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD version VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:version)\', ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE data_dictionary_version ADD CONSTRAINT FK_74CABF5D16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_version ADD CONSTRAINT FK_74CABF5DD095DFFF FOREIGN KEY (data_dictionary) REFERENCES data_dictionary (id)');
        $this->addSql('ALTER TABLE data_dictionary_version ADD CONSTRAINT FK_74CABF5DDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_74CABF5DDE12AB56 ON data_dictionary_version (created_by)');
        $this->addSql('CREATE INDEX IDX_74CABF5DD095DFFF ON data_dictionary_version (data_dictionary)');
        $this->addSql('CREATE INDEX IDX_74CABF5D16FE72E1 ON data_dictionary_version (updated_by)');
        $this->addSql('ALTER TABLE data_model ADD created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE data_model ADD CONSTRAINT FK_992ABE4616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model ADD CONSTRAINT FK_992ABE46DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_992ABE4616FE72E1 ON data_model (updated_by)');
        $this->addSql('CREATE INDEX IDX_992ABE46DE12AB56 ON data_model (created_by)');
        $this->addSql('ALTER TABLE data_model_module ADD data_model CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD dependencies CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD `order` INT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL, ADD is_repeated TINYINT(1) DEFAULT \'0\' NOT NULL, ADD is_dependent TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A6616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A66992ABE46 FOREIGN KEY (data_model) REFERENCES data_model_version (id)');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A66DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A66EA0F708D FOREIGN KEY (dependencies) REFERENCES data_model_dependency_group (id)');
        $this->addSql('CREATE INDEX IDX_B9356A66992ABE46 ON data_model_module (data_model)');
        $this->addSql('CREATE INDEX IDX_B9356A6616FE72E1 ON data_model_module (updated_by)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B9356A66EA0F708D ON data_model_module (dependencies)');
        $this->addSql('CREATE INDEX IDX_B9356A66DE12AB56 ON data_model_module (created_by)');
        $this->addSql('ALTER TABLE data_model_node ADD data_model CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL, ADD dtype VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_model_node ADD CONSTRAINT FK_671DFE7B16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_node ADD CONSTRAINT FK_671DFE7B992ABE46 FOREIGN KEY (data_model) REFERENCES data_model_version (id)');
        $this->addSql('ALTER TABLE data_model_node ADD CONSTRAINT FK_671DFE7BDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_671DFE7BDE12AB56 ON data_model_node (created_by)');
        $this->addSql('CREATE INDEX IDX_671DFE7B992ABE46 ON data_model_node (data_model)');
        $this->addSql('CREATE INDEX IDX_671DFE7B16FE72E1 ON data_model_node (updated_by)');
        $this->addSql('ALTER TABLE data_model_node_external DROP FOREIGN KEY FK_FD7D7D44BF396750');
        $this->addSql('ALTER TABLE data_model_node_external ADD CONSTRAINT FK_FD7D7D44BF396750 FOREIGN KEY (id) REFERENCES data_model_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_internal DROP FOREIGN KEY FK_AEDCC1FFBF396750');
        $this->addSql('ALTER TABLE data_model_node_internal ADD CONSTRAINT FK_AEDCC1FFBF396750 FOREIGN KEY (id) REFERENCES data_model_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_literal DROP FOREIGN KEY FK_B4D5BACFBF396750');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_model_node_literal ADD CONSTRAINT FK_B4D5BACFBF396750 FOREIGN KEY (id) REFERENCES data_model_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_record DROP FOREIGN KEY FK_9D711823BF396750');
        $this->addSql('ALTER TABLE data_model_node_record ADD CONSTRAINT FK_9D711823BF396750 FOREIGN KEY (id) REFERENCES data_model_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_value DROP FOREIGN KEY FK_EE45F571BF396750');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_model_node_value ADD CONSTRAINT FK_EE45F571BF396750 FOREIGN KEY (id) REFERENCES data_model_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_triple ADD module CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE data_model_triple ADD CONSTRAINT FK_F13D703016FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_triple ADD CONSTRAINT FK_F13D7030C242628 FOREIGN KEY (module) REFERENCES data_model_module (id)');
        $this->addSql('ALTER TABLE data_model_triple ADD CONSTRAINT FK_F13D7030DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F13D703016FE72E1 ON data_model_triple (updated_by)');
        $this->addSql('CREATE INDEX IDX_F13D7030DE12AB56 ON data_model_triple (created_by)');
        $this->addSql('CREATE INDEX IDX_F13D7030C242628 ON data_model_triple (module)');
        $this->addSql('ALTER TABLE data_model_version ADD data_model CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD updated_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD version VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:version)\', ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE data_model_version ADD CONSTRAINT FK_2ECDAE1816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_version ADD CONSTRAINT FK_2ECDAE18992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id)');
        $this->addSql('ALTER TABLE data_model_version ADD CONSTRAINT FK_2ECDAE18DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2ECDAE18DE12AB56 ON data_model_version (created_by)');
        $this->addSql('CREATE INDEX IDX_2ECDAE18992ABE46 ON data_model_version (data_model)');
        $this->addSql('CREATE INDEX IDX_2ECDAE1816FE72E1 ON data_model_version (updated_by)');
        $this->addSql('DROP INDEX IDX_91757299FF7D0EB0 ON distribution_contents');
        $this->addSql('DROP INDEX IDX_91757299304546D7 ON distribution_contents');
        $this->addSql('ALTER TABLE distribution_contents DROP data_specification, DROP data_specification_version');
        $this->addSql('ALTER TABLE distribution_csv ADD data_dictionary CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD data_dictionary_version CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_csv ADD CONSTRAINT FK_D815CB1A74CABF5D FOREIGN KEY (data_dictionary_version) REFERENCES data_dictionary_version (id)');
        $this->addSql('ALTER TABLE distribution_csv ADD CONSTRAINT FK_D815CB1AD095DFFF FOREIGN KEY (data_dictionary) REFERENCES data_dictionary (id)');
        $this->addSql('CREATE INDEX IDX_D815CB1A74CABF5D ON distribution_csv (data_dictionary_version)');
        $this->addSql('CREATE INDEX IDX_D815CB1AD095DFFF ON distribution_csv (data_dictionary)');
        $this->addSql('ALTER TABLE distribution_rdf ADD data_model CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD data_model_version CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AF2ECDAE18 FOREIGN KEY (data_model_version) REFERENCES data_model_version (id)');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AF992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id)');
        $this->addSql('CREATE INDEX IDX_DDC596AF2ECDAE18 ON distribution_rdf (data_model_version)');
        $this->addSql('CREATE INDEX IDX_DDC596AF992ABE46 ON distribution_rdf (data_model)');
    }

    protected function setForeignKeyChecks(bool $enabled): void
    {
        $connection = $this->connection;
        $platform = $connection->getDatabasePlatform();

        if (! ($platform instanceof MySqlPlatform)) {
            return;
        }

        $connection->executeStatement(sprintf('SET FOREIGN_KEY_CHECKS = %s;', (int) $enabled));
    }
}
