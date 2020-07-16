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
final class Version20200715082053 extends AbstractMigration
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
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE data_model_version (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, version VARCHAR(255) NOT NULL COMMENT \'(DC2Type:version)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_2ECDAE18992ABE46 (data_model), INDEX IDX_2ECDAE18DE12AB56 (created_by), INDEX IDX_2ECDAE1816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE data_model_version ADD CONSTRAINT FK_2ECDAE18992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id)');
        $this->addSql('ALTER TABLE data_model_version ADD CONSTRAINT FK_2ECDAE18DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_version ADD CONSTRAINT FK_2ECDAE1816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_module DROP FOREIGN KEY FK_B9356A66992ABE46');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A66992ABE46 FOREIGN KEY (data_model) REFERENCES data_model_version (id)');
        $this->addSql('ALTER TABLE data_model_prefix DROP FOREIGN KEY FK_26A0CAC0992ABE46');
        $this->addSql('ALTER TABLE data_model_prefix ADD CONSTRAINT FK_26A0CAC0992ABE46 FOREIGN KEY (data_model) REFERENCES data_model_version (id)');
        $this->addSql('ALTER TABLE data_model_node DROP FOREIGN KEY FK_671DFE7B992ABE46');
        $this->addSql('ALTER TABLE data_model_node ADD CONSTRAINT FK_671DFE7B992ABE46 FOREIGN KEY (data_model) REFERENCES data_model_version (id)');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_predicate DROP FOREIGN KEY FK_7C632AFF992ABE46');
        $this->addSql('ALTER TABLE data_model_predicate ADD CONSTRAINT FK_7C632AFF992ABE46 FOREIGN KEY (data_model) REFERENCES data_model_version (id)');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AF992ABE46');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AF992ABE46 FOREIGN KEY (data_model) REFERENCES data_model_version (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_model_module DROP FOREIGN KEY FK_B9356A66992ABE46');
        $this->addSql('ALTER TABLE data_model_prefix DROP FOREIGN KEY FK_26A0CAC0992ABE46');
        $this->addSql('ALTER TABLE data_model_node DROP FOREIGN KEY FK_671DFE7B992ABE46');
        $this->addSql('ALTER TABLE data_model_predicate DROP FOREIGN KEY FK_7C632AFF992ABE46');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AF992ABE46');
        $this->addSql('DROP TABLE data_model_version');
        $this->addSql('ALTER TABLE data_model_module DROP FOREIGN KEY FK_B9356A66992ABE46');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A66992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE data_model_node DROP FOREIGN KEY FK_671DFE7B992ABE46');
        $this->addSql('ALTER TABLE data_model_node ADD CONSTRAINT FK_671DFE7B992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_predicate DROP FOREIGN KEY FK_7C632AFF992ABE46');
        $this->addSql('ALTER TABLE data_model_predicate ADD CONSTRAINT FK_7C632AFF992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE data_model_prefix DROP FOREIGN KEY FK_26A0CAC0992ABE46');
        $this->addSql('ALTER TABLE data_model_prefix ADD CONSTRAINT FK_26A0CAC0992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AF992ABE46');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AF992ABE46 FOREIGN KEY (data_model) REFERENCES data_model (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
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
