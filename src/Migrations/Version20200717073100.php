<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200717073100 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE TABLE distribution_rdf_mappings');

        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD module CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD type VARCHAR(255) NOT NULL, CHANGE data_model_version data_model_version CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD CONSTRAINT FK_DA9D49EFC242628 FOREIGN KEY (module) REFERENCES data_model_module (id)');
        $this->addSql('CREATE INDEX IDX_DA9D49EFC242628 ON distribution_rdf_mappings (module)');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP FOREIGN KEY FK_DA9D49EFC242628');
        $this->addSql('DROP INDEX IDX_DA9D49EFC242628 ON distribution_rdf_mappings');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP id, DROP module, DROP type, CHANGE data_model_version data_model_version CHAR(36) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD PRIMARY KEY (distribution, node, entity, data_model_version)');
    }
}
