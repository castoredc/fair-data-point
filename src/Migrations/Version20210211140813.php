<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210211140813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE data_specification_mappings_element (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', element CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', syntax LONGTEXT DEFAULT NULL, transform_data TINYINT(1) NOT NULL, INDEX IDX_A56F1D5341405E39 (element), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE element_mapping_castor_entity (element_mapping_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', castor_entity_id VARCHAR(190) NOT NULL, INDEX IDX_57A8637756AF85A2 (element_mapping_id), INDEX IDX_57A86377380FC0DA (castor_entity_id), PRIMARY KEY(element_mapping_id, castor_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_specification_mappings_group (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', entity VARCHAR(190) NOT NULL, groupId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_55557DF1ED8188B0 (groupId), INDEX IDX_55557DF1E284468 (entity), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE data_specification_mappings_element ADD CONSTRAINT FK_A56F1D5341405E39 FOREIGN KEY (element) REFERENCES data_specification_element (id)');
        $this->addSql('ALTER TABLE data_specification_mappings_element ADD CONSTRAINT FK_A56F1D53BF396750 FOREIGN KEY (id) REFERENCES data_specification_mappings (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE element_mapping_castor_entity ADD CONSTRAINT FK_57A8637756AF85A2 FOREIGN KEY (element_mapping_id) REFERENCES data_specification_mappings_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE element_mapping_castor_entity ADD CONSTRAINT FK_57A86377380FC0DA FOREIGN KEY (castor_entity_id) REFERENCES castor_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_specification_mappings_group ADD CONSTRAINT FK_55557DF1ED8188B0 FOREIGN KEY (groupId) REFERENCES data_specification_group (id)');
        $this->addSql('ALTER TABLE data_specification_mappings_group ADD CONSTRAINT FK_55557DF1E284468 FOREIGN KEY (entity) REFERENCES castor_entity (id)');
        $this->addSql('ALTER TABLE data_specification_mappings_group ADD CONSTRAINT FK_55557DF1BF396750 FOREIGN KEY (id) REFERENCES data_specification_mappings (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE data_transformation_mapping_element');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_specification_mappings DROP FOREIGN KEY FK_C7FCEF141405E39');
        $this->addSql('ALTER TABLE data_specification_mappings DROP FOREIGN KEY FK_C7FCEF1E284468');
        $this->addSql('ALTER TABLE data_specification_mappings DROP FOREIGN KEY FK_C7FCEF1ED8188B0');
        $this->addSql('DROP INDEX IDX_C7FCEF1E284468 ON data_specification_mappings');
        $this->addSql('DROP INDEX IDX_C7FCEF141405E39 ON data_specification_mappings');
        $this->addSql('DROP INDEX IDX_C7FCEF1ED8188B0 ON data_specification_mappings');
        $this->addSql('ALTER TABLE data_specification_mappings DROP entity, DROP element, DROP groupId, DROP syntax');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_mapping_castor_entity DROP FOREIGN KEY FK_57A8637756AF85A2');
        $this->addSql('CREATE TABLE data_transformation_mapping_element (data_transformation_mapping_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', element_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', INDEX IDX_19FCE9EA83FD70F0 (data_transformation_mapping_id), INDEX IDX_19FCE9EA1F1F2A24 (element_id), PRIMARY KEY(data_transformation_mapping_id, element_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE data_transformation_mapping_element ADD CONSTRAINT FK_19FCE9EA1F1F2A24 FOREIGN KEY (element_id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_transformation_mapping_element ADD CONSTRAINT FK_19FCE9EA83FD70F0 FOREIGN KEY (data_transformation_mapping_id) REFERENCES data_specification_mappings (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE data_specification_mappings_element');
        $this->addSql('DROP TABLE element_mapping_castor_entity');
        $this->addSql('DROP TABLE data_specification_mappings_group');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_specification_mappings ADD entity VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD element CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD groupId CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD syntax LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF141405E39 FOREIGN KEY (element) REFERENCES data_specification_element (id)');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF1E284468 FOREIGN KEY (entity) REFERENCES castor_entity (id)');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF1ED8188B0 FOREIGN KEY (groupId) REFERENCES data_specification_group (id)');
        $this->addSql('CREATE INDEX IDX_C7FCEF1E284468 ON data_specification_mappings (entity)');
        $this->addSql('CREATE INDEX IDX_C7FCEF141405E39 ON data_specification_mappings (element)');
        $this->addSql('CREATE INDEX IDX_C7FCEF1ED8188B0 ON data_specification_mappings (groupId)');
    }
}
