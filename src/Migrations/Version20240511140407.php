<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240511140407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_model_version ADD catalog_title_node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD dataset_title_node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD distribution_title_node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD fdp_title_node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_model_version ADD CONSTRAINT FK_82AFF5787B8FC0E1 FOREIGN KEY (catalog_title_node) REFERENCES metadata_model_node_value (id)');
        $this->addSql('ALTER TABLE metadata_model_version ADD CONSTRAINT FK_82AFF578EB5E1B35 FOREIGN KEY (dataset_title_node) REFERENCES metadata_model_node_value (id)');
        $this->addSql('ALTER TABLE metadata_model_version ADD CONSTRAINT FK_82AFF578DAE3F935 FOREIGN KEY (distribution_title_node) REFERENCES metadata_model_node_value (id)');
        $this->addSql('ALTER TABLE metadata_model_version ADD CONSTRAINT FK_82AFF57889CDFFCA FOREIGN KEY (fdp_title_node) REFERENCES metadata_model_node_value (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_82AFF5787B8FC0E1 ON metadata_model_version (catalog_title_node)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_82AFF578EB5E1B35 ON metadata_model_version (dataset_title_node)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_82AFF578DAE3F935 ON metadata_model_version (distribution_title_node)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_82AFF57889CDFFCA ON metadata_model_version (fdp_title_node)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_model_version DROP FOREIGN KEY FK_82AFF5787B8FC0E1');
        $this->addSql('ALTER TABLE metadata_model_version DROP FOREIGN KEY FK_82AFF578EB5E1B35');
        $this->addSql('ALTER TABLE metadata_model_version DROP FOREIGN KEY FK_82AFF578DAE3F935');
        $this->addSql('ALTER TABLE metadata_model_version DROP FOREIGN KEY FK_82AFF57889CDFFCA');
        $this->addSql('DROP INDEX UNIQ_82AFF5787B8FC0E1 ON metadata_model_version');
        $this->addSql('DROP INDEX UNIQ_82AFF578EB5E1B35 ON metadata_model_version');
        $this->addSql('DROP INDEX UNIQ_82AFF578DAE3F935 ON metadata_model_version');
        $this->addSql('DROP INDEX UNIQ_82AFF57889CDFFCA ON metadata_model_version');
        $this->addSql('ALTER TABLE metadata_model_version DROP catalog_title_node, DROP dataset_title_node, DROP distribution_title_node, DROP fdp_title_node');
    }
}
