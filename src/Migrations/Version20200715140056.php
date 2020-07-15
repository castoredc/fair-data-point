<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200715140056 extends AbstractMigration
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

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD data_model_version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD CONSTRAINT FK_DA9D49EF2ECDAE18 FOREIGN KEY (data_model_version) REFERENCES data_model_version (id)');
        $this->addSql('CREATE INDEX IDX_DA9D49EF2ECDAE18 ON distribution_rdf_mappings (data_model_version)');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD PRIMARY KEY (distribution, node, entity, data_model_version)');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AF992ABE46');
        $this->addSql('ALTER TABLE distribution_rdf ADD data_model_version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AF2ECDAE18 FOREIGN KEY (data_model_version) REFERENCES data_model_version (id)');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AF992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id)');
        $this->addSql('CREATE INDEX IDX_DDC596AF2ECDAE18 ON distribution_rdf (data_model_version)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AF2ECDAE18');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AF992ABE46');
        $this->addSql('DROP INDEX IDX_DDC596AF2ECDAE18 ON distribution_rdf');
        $this->addSql('ALTER TABLE distribution_rdf DROP data_model_version');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AF992ABE46 FOREIGN KEY (data_model) REFERENCES data_model_version (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP FOREIGN KEY FK_DA9D49EF2ECDAE18');
        $this->addSql('DROP INDEX IDX_DA9D49EF2ECDAE18 ON distribution_rdf_mappings');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP data_model_version');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD PRIMARY KEY (distribution, node, entity)');
    }

    protected function setForeignKeyChecks(bool $enabled): void
    {
        $connection = $this->connection;
        $platform = $connection->getDatabasePlatform();

        if (! ($platform instanceof MySqlPlatform)) {
            return;
        }

        $connection->exec(sprintf('SET FOREIGN_KEY_CHECKS = %s;', (int) $enabled));
    }
}
