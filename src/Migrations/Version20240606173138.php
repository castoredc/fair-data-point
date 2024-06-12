<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606173138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metadata_model_display_setting (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', metadata_model CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', node_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', orderNumber INT DEFAULT NULL, resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\', display_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:MetadataDisplayType)\', display_position VARCHAR(255) NOT NULL COMMENT \'(DC2Type:MetadataDisplayPosition)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_4A8581B835F6CABB (metadata_model), UNIQUE INDEX UNIQ_4A8581B8460D9FD7 (node_id), INDEX IDX_4A8581B8DE12AB56 (created_by), INDEX IDX_4A8581B816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_model_display_setting ADD CONSTRAINT FK_4A8581B835F6CABB FOREIGN KEY (metadata_model) REFERENCES metadata_model_version (id)');
        $this->addSql('ALTER TABLE metadata_model_display_setting ADD CONSTRAINT FK_4A8581B8460D9FD7 FOREIGN KEY (node_id) REFERENCES metadata_model_node_value (id)');
        $this->addSql('ALTER TABLE metadata_model_display_setting ADD CONSTRAINT FK_4A8581B8DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_display_setting ADD CONSTRAINT FK_4A8581B816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_version DROP FOREIGN KEY FK_82AFF5787B8FC0E1');
        $this->addSql('ALTER TABLE metadata_model_version DROP FOREIGN KEY FK_82AFF57889CDFFCA');
        $this->addSql('ALTER TABLE metadata_model_version DROP FOREIGN KEY FK_82AFF578DAE3F935');
        $this->addSql('ALTER TABLE metadata_model_version DROP FOREIGN KEY FK_82AFF578EB5E1B35');
        $this->addSql('DROP INDEX UNIQ_82AFF5787B8FC0E1 ON metadata_model_version');
        $this->addSql('DROP INDEX UNIQ_82AFF57889CDFFCA ON metadata_model_version');
        $this->addSql('DROP INDEX UNIQ_82AFF578DAE3F935 ON metadata_model_version');
        $this->addSql('DROP INDEX UNIQ_82AFF578EB5E1B35 ON metadata_model_version');
        $this->addSql('ALTER TABLE metadata_model_version DROP catalog_title_node, DROP dataset_title_node, DROP distribution_title_node, DROP fdp_title_node');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_model_display_setting DROP FOREIGN KEY FK_4A8581B835F6CABB');
        $this->addSql('ALTER TABLE metadata_model_display_setting DROP FOREIGN KEY FK_4A8581B8460D9FD7');
        $this->addSql('ALTER TABLE metadata_model_display_setting DROP FOREIGN KEY FK_4A8581B8DE12AB56');
        $this->addSql('ALTER TABLE metadata_model_display_setting DROP FOREIGN KEY FK_4A8581B816FE72E1');
        $this->addSql('DROP TABLE metadata_model_display_setting');
        $this->addSql('ALTER TABLE metadata_model_version ADD catalog_title_node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD dataset_title_node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD distribution_title_node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD fdp_title_node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_model_version ADD CONSTRAINT FK_82AFF5787B8FC0E1 FOREIGN KEY (catalog_title_node) REFERENCES metadata_model_node_value (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE metadata_model_version ADD CONSTRAINT FK_82AFF57889CDFFCA FOREIGN KEY (fdp_title_node) REFERENCES metadata_model_node_value (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE metadata_model_version ADD CONSTRAINT FK_82AFF578DAE3F935 FOREIGN KEY (distribution_title_node) REFERENCES metadata_model_node_value (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE metadata_model_version ADD CONSTRAINT FK_82AFF578EB5E1B35 FOREIGN KEY (dataset_title_node) REFERENCES metadata_model_node_value (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_82AFF5787B8FC0E1 ON metadata_model_version (catalog_title_node)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_82AFF57889CDFFCA ON metadata_model_version (fdp_title_node)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_82AFF578DAE3F935 ON metadata_model_version (distribution_title_node)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_82AFF578EB5E1B35 ON metadata_model_version (dataset_title_node)');
    }
}
