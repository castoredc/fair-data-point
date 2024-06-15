<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240615103453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metadata_model_display_setting (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', metadata_model CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', node_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, orderNumber INT DEFAULT NULL, resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\', display_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:MetadataDisplayType)\', display_position VARCHAR(255) NOT NULL COMMENT \'(DC2Type:MetadataDisplayPosition)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_4A8581B835F6CABB (metadata_model), UNIQUE INDEX UNIQ_4A8581B8460D9FD7 (node_id), INDEX IDX_4A8581B8DE12AB56 (created_by), INDEX IDX_4A8581B816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_field (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', metadata_model CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', form_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', option_group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', node_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, orderNumber INT DEFAULT NULL, resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\', field_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:MetadataFieldType)\', is_required TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_83C8D40835F6CABB (metadata_model), INDEX IDX_83C8D4085FF69B7D (form_id), INDEX IDX_83C8D408DE23A8E3 (option_group_id), UNIQUE INDEX UNIQ_83C8D408460D9FD7 (node_id), INDEX IDX_83C8D408DE12AB56 (created_by), INDEX IDX_83C8D40816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_form (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', metadata_model CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, orderNumber INT DEFAULT NULL, resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_623277E735F6CABB (metadata_model), INDEX IDX_623277E7DE12AB56 (created_by), INDEX IDX_623277E716FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_node_children (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_node_internal (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', slug VARCHAR(255) NOT NULL, is_repeated TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_node_parents (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_value (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', value_node_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', value LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_FF14E056DC9EE959 (metadata_id), INDEX IDX_FF14E056ADAFEAB7 (value_node_id), INDEX IDX_FF14E056DE12AB56 (created_by), INDEX IDX_FF14E05616FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_model_display_setting ADD CONSTRAINT FK_4A8581B835F6CABB FOREIGN KEY (metadata_model) REFERENCES metadata_model_version (id)');
        $this->addSql('ALTER TABLE metadata_model_display_setting ADD CONSTRAINT FK_4A8581B8460D9FD7 FOREIGN KEY (node_id) REFERENCES metadata_model_node_value (id)');
        $this->addSql('ALTER TABLE metadata_model_display_setting ADD CONSTRAINT FK_4A8581B8DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_display_setting ADD CONSTRAINT FK_4A8581B816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D40835F6CABB FOREIGN KEY (metadata_model) REFERENCES metadata_model_version (id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D4085FF69B7D FOREIGN KEY (form_id) REFERENCES metadata_model_form (id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D408DE23A8E3 FOREIGN KEY (option_group_id) REFERENCES metadata_model_option_group (id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D408460D9FD7 FOREIGN KEY (node_id) REFERENCES metadata_model_node_value (id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D408DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D40816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_form ADD CONSTRAINT FK_623277E735F6CABB FOREIGN KEY (metadata_model) REFERENCES metadata_model_version (id)');
        $this->addSql('ALTER TABLE metadata_model_form ADD CONSTRAINT FK_623277E7DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_form ADD CONSTRAINT FK_623277E716FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_node_children ADD CONSTRAINT FK_2C2074ABF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_node_internal ADD CONSTRAINT FK_A8A6ABF3BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_node_parents ADD CONSTRAINT FK_14E69908BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_value ADD CONSTRAINT FK_FF14E056DC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata (id)');
        $this->addSql('ALTER TABLE metadata_value ADD CONSTRAINT FK_FF14E056ADAFEAB7 FOREIGN KEY (value_node_id) REFERENCES metadata_model_node_value (id)');
        $this->addSql('ALTER TABLE metadata_value ADD CONSTRAINT FK_FF14E056DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_value ADD CONSTRAINT FK_FF14E05616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE catalog ADD default_metadata_model_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32473F018586 FOREIGN KEY (default_metadata_model_id) REFERENCES metadata_model (id)');
        $this->addSql('CREATE INDEX IDX_1B2C32473F018586 ON catalog (default_metadata_model_id)');
        $this->addSql('ALTER TABLE dataset ADD default_metadata_model_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D03F018586 FOREIGN KEY (default_metadata_model_id) REFERENCES metadata_model (id)');
        $this->addSql('CREATE INDEX IDX_B7A041D03F018586 ON dataset (default_metadata_model_id)');
        $this->addSql('ALTER TABLE distribution ADD default_metadata_model_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837813F018586 FOREIGN KEY (default_metadata_model_id) REFERENCES metadata_model (id)');
        $this->addSql('CREATE INDEX IDX_A44837813F018586 ON distribution (default_metadata_model_id)');
        $this->addSql('ALTER TABLE fdp ADD default_metadata_model_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F3F018586 FOREIGN KEY (default_metadata_model_id) REFERENCES metadata_model (id)');
        $this->addSql('CREATE INDEX IDX_E28FB11F3F018586 ON fdp (default_metadata_model_id)');
        $this->addSql('ALTER TABLE metadata ADD metadata_model_version_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F143414130157DB FOREIGN KEY (metadata_model_version_id) REFERENCES metadata_model_version (id)');
        $this->addSql('CREATE INDEX IDX_4F143414130157DB ON metadata (metadata_model_version_id)');
        $this->addSql('ALTER TABLE metadata_model_module ADD resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\'');
        $this->addSql('ALTER TABLE metadata_model_node_record ADD resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\'');
        $this->addSql('ALTER TABLE metadata_model_node_value DROP field_type');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2B16FE72E1');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2BDE12AB56');
        $this->addSql('DROP INDEX IDX_41C2F2BDE12AB56 ON metadata_study');
        $this->addSql('DROP INDEX IDX_41C2F2B16FE72E1 ON metadata_study');
        $this->addSql('ALTER TABLE metadata_study DROP created_by, DROP updated_by, DROP updated_at, DROP version, DROP created_at');
        $this->addSql('INSERT INTO metadata (id, version, created_at, dtype) SELECT id, \'0.0.0\', NOW(), \'studymetadata\' FROM metadata_study');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2BBF396750 FOREIGN KEY (id) REFERENCES metadata (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ontology DROP `0454bc56-454b-48cd-9de3-b8b6c288db13`, DROP `http://purl.bioontology.org/ontology/PATO/`, DROP `Phenotypic Quality Ontology`, DROP `PATO`');
        $this->addSql('ALTER TABLE study ADD default_metadata_model_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F97493F018586 FOREIGN KEY (default_metadata_model_id) REFERENCES metadata_model (id)');
        $this->addSql('CREATE INDEX IDX_E67F97493F018586 ON study (default_metadata_model_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_model_display_setting DROP FOREIGN KEY FK_4A8581B835F6CABB');
        $this->addSql('ALTER TABLE metadata_model_display_setting DROP FOREIGN KEY FK_4A8581B8460D9FD7');
        $this->addSql('ALTER TABLE metadata_model_display_setting DROP FOREIGN KEY FK_4A8581B8DE12AB56');
        $this->addSql('ALTER TABLE metadata_model_display_setting DROP FOREIGN KEY FK_4A8581B816FE72E1');
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D40835F6CABB');
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D4085FF69B7D');
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D408DE23A8E3');
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D408460D9FD7');
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D408DE12AB56');
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D40816FE72E1');
        $this->addSql('ALTER TABLE metadata_model_form DROP FOREIGN KEY FK_623277E735F6CABB');
        $this->addSql('ALTER TABLE metadata_model_form DROP FOREIGN KEY FK_623277E7DE12AB56');
        $this->addSql('ALTER TABLE metadata_model_form DROP FOREIGN KEY FK_623277E716FE72E1');
        $this->addSql('ALTER TABLE metadata_model_node_children DROP FOREIGN KEY FK_2C2074ABF396750');
        $this->addSql('ALTER TABLE metadata_model_node_internal DROP FOREIGN KEY FK_A8A6ABF3BF396750');
        $this->addSql('ALTER TABLE metadata_model_node_parents DROP FOREIGN KEY FK_14E69908BF396750');
        $this->addSql('ALTER TABLE metadata_value DROP FOREIGN KEY FK_FF14E056DC9EE959');
        $this->addSql('ALTER TABLE metadata_value DROP FOREIGN KEY FK_FF14E056ADAFEAB7');
        $this->addSql('ALTER TABLE metadata_value DROP FOREIGN KEY FK_FF14E056DE12AB56');
        $this->addSql('ALTER TABLE metadata_value DROP FOREIGN KEY FK_FF14E05616FE72E1');
        $this->addSql('DROP TABLE metadata_model_display_setting');
        $this->addSql('DROP TABLE metadata_model_field');
        $this->addSql('DROP TABLE metadata_model_form');
        $this->addSql('DROP TABLE metadata_model_node_children');
        $this->addSql('DROP TABLE metadata_model_node_internal');
        $this->addSql('DROP TABLE metadata_model_node_parents');
        $this->addSql('DROP TABLE metadata_value');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32473F018586');
        $this->addSql('DROP INDEX IDX_1B2C32473F018586 ON catalog');
        $this->addSql('ALTER TABLE catalog DROP default_metadata_model_id');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D03F018586');
        $this->addSql('DROP INDEX IDX_B7A041D03F018586 ON dataset');
        $this->addSql('ALTER TABLE dataset DROP default_metadata_model_id');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837813F018586');
        $this->addSql('DROP INDEX IDX_A44837813F018586 ON distribution');
        $this->addSql('ALTER TABLE distribution DROP default_metadata_model_id');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F3F018586');
        $this->addSql('DROP INDEX IDX_E28FB11F3F018586 ON fdp');
        $this->addSql('ALTER TABLE fdp DROP default_metadata_model_id');
        $this->addSql('ALTER TABLE metadata DROP FOREIGN KEY FK_4F143414130157DB');
        $this->addSql('DROP INDEX IDX_4F143414130157DB ON metadata');
        $this->addSql('ALTER TABLE metadata DROP metadata_model_version_id');
        $this->addSql('ALTER TABLE metadata_model_module DROP resource_type');
        $this->addSql('ALTER TABLE metadata_model_node_record DROP resource_type');
        $this->addSql('ALTER TABLE metadata_model_node_value ADD field_type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:MetadataFieldType)\'');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2BBF396750');
        $this->addSql('ALTER TABLE metadata_study ADD created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD updated_at DATETIME DEFAULT NULL, ADD version VARCHAR(255) NOT NULL COMMENT \'(DC2Type:version)\', ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2B16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2BDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_41C2F2BDE12AB56 ON metadata_study (created_by)');
        $this->addSql('CREATE INDEX IDX_41C2F2B16FE72E1 ON metadata_study (updated_by)');
        $this->addSql('ALTER TABLE ontology ADD 0454bc56-454b-48cd-9de3-b8b6c288db13 VARCHAR(1024) DEFAULT NULL, ADD http://purl.bioontology VARCHAR(1024) DEFAULT NULL, ADD Phenotypic Quality Ontology VARCHAR(1024) DEFAULT NULL, ADD PATO VARCHAR(1024) DEFAULT NULL');
        $this->addSql('ALTER TABLE study DROP FOREIGN KEY FK_E67F97493F018586');
        $this->addSql('DROP INDEX IDX_E67F97493F018586 ON study');
        $this->addSql('ALTER TABLE study DROP default_metadata_model_id');
    }
}
