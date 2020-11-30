<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201118131635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_specification_element DROP FOREIGN KEY FK_CE19CB2D6DC044C5');
        $this->addSql('DROP INDEX IDX_CE19CB2D6DC044C5 ON data_specification_element');
        $this->addSql('ALTER TABLE data_specification_element ADD orderNumber INT DEFAULT NULL, DROP `order`, CHANGE `group` groupId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_element ADD CONSTRAINT FK_CE19CB2DED8188B0 FOREIGN KEY (groupId) REFERENCES data_specification_group (id)');
        $this->addSql('CREATE INDEX IDX_CE19CB2DED8188B0 ON data_specification_element (groupId)');
        $this->addSql('ALTER TABLE data_specification_elementgroup DROP FOREIGN KEY FK_B353A4486DC044C5');
        $this->addSql('DROP INDEX IDX_B353A4486DC044C5 ON data_specification_elementgroup');
        $this->addSql('ALTER TABLE data_specification_elementgroup CHANGE `group` groupId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_elementgroup ADD CONSTRAINT FK_B353A448ED8188B0 FOREIGN KEY (groupId) REFERENCES data_specification_group (id)');
        $this->addSql('CREATE INDEX IDX_B353A448ED8188B0 ON data_specification_elementgroup (groupId)');
        $this->addSql('ALTER TABLE data_specification_group ADD orderNumber INT DEFAULT NULL, DROP `order`');
        $this->addSql('ALTER TABLE data_specification_mappings DROP FOREIGN KEY FK_C7FCEF16DC044C5');
        $this->addSql('DROP INDEX IDX_C7FCEF16DC044C5 ON data_specification_mappings');
        $this->addSql('ALTER TABLE data_specification_mappings CHANGE `group` groupId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF1ED8188B0 FOREIGN KEY (groupId) REFERENCES data_specification_group (id)');
        $this->addSql('CREATE INDEX IDX_C7FCEF1ED8188B0 ON data_specification_mappings (groupId)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_specification_element DROP FOREIGN KEY FK_CE19CB2DED8188B0');
        $this->addSql('DROP INDEX IDX_CE19CB2DED8188B0 ON data_specification_element');
        $this->addSql('ALTER TABLE data_specification_element ADD `order` INT NOT NULL, DROP orderNumber, CHANGE groupid `group` CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_element ADD CONSTRAINT FK_CE19CB2D6DC044C5 FOREIGN KEY (`group`) REFERENCES data_specification_group (id)');
        $this->addSql('CREATE INDEX IDX_CE19CB2D6DC044C5 ON data_specification_element (`group`)');
        $this->addSql('ALTER TABLE data_specification_elementgroup DROP FOREIGN KEY FK_B353A448ED8188B0');
        $this->addSql('DROP INDEX IDX_B353A448ED8188B0 ON data_specification_elementgroup');
        $this->addSql('ALTER TABLE data_specification_elementgroup CHANGE groupid `group` CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_elementgroup ADD CONSTRAINT FK_B353A4486DC044C5 FOREIGN KEY (`group`) REFERENCES data_specification_group (id)');
        $this->addSql('CREATE INDEX IDX_B353A4486DC044C5 ON data_specification_elementgroup (`group`)');
        $this->addSql('ALTER TABLE data_specification_group ADD `order` INT NOT NULL, DROP orderNumber');
        $this->addSql('ALTER TABLE data_specification_mappings DROP FOREIGN KEY FK_C7FCEF1ED8188B0');
        $this->addSql('DROP INDEX IDX_C7FCEF1ED8188B0 ON data_specification_mappings');
        $this->addSql('ALTER TABLE data_specification_mappings CHANGE groupid `group` CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_mappings ADD CONSTRAINT FK_C7FCEF16DC044C5 FOREIGN KEY (`group`) REFERENCES data_specification_group (id)');
        $this->addSql('CREATE INDEX IDX_C7FCEF16DC044C5 ON data_specification_mappings (`group`)');
    }
}
