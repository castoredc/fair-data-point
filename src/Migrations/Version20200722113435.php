<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200722113435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE data_model_dependency (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_C5C16ABCFE54D947 (group_id), INDEX IDX_C5C16ABCDE12AB56 (created_by), INDEX IDX_C5C16ABC16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_dependency_group (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', combinator VARCHAR(255) NOT NULL COMMENT \'(DC2Type:DependencyCombinatorType)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_model_dependency_rule (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', node CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', operator VARCHAR(255) NOT NULL COMMENT \'(DC2Type:DependencyOperatorType)\', value VARCHAR(255) NOT NULL, INDEX IDX_BCB1F1D9857FE845 (node), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE data_model_dependency ADD CONSTRAINT FK_C5C16ABCFE54D947 FOREIGN KEY (group_id) REFERENCES data_model_dependency_group (id)');
        $this->addSql('ALTER TABLE data_model_dependency ADD CONSTRAINT FK_C5C16ABCDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_dependency ADD CONSTRAINT FK_C5C16ABC16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_model_dependency_group ADD CONSTRAINT FK_E7C973BF396750 FOREIGN KEY (id) REFERENCES data_model_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_dependency_rule ADD CONSTRAINT FK_BCB1F1D9857FE845 FOREIGN KEY (node) REFERENCES data_model_node_value (id)');
        $this->addSql('ALTER TABLE data_model_dependency_rule ADD CONSTRAINT FK_BCB1F1D9BF396750 FOREIGN KEY (id) REFERENCES data_model_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_module ADD dependencies CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', ADD is_dependent TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE data_model_module ADD CONSTRAINT FK_B9356A66EA0F708D FOREIGN KEY (dependencies) REFERENCES data_model_dependency_group (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B9356A66EA0F708D ON data_model_module (dependencies)');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE distribution_rdf_mappings CHANGE data_model_version data_model_version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_model_dependency_group DROP FOREIGN KEY FK_E7C973BF396750');
        $this->addSql('ALTER TABLE data_model_dependency_rule DROP FOREIGN KEY FK_BCB1F1D9BF396750');
        $this->addSql('ALTER TABLE data_model_module DROP FOREIGN KEY FK_B9356A66EA0F708D');
        $this->addSql('ALTER TABLE data_model_dependency DROP FOREIGN KEY FK_C5C16ABCFE54D947');
        $this->addSql('DROP TABLE data_model_dependency');
        $this->addSql('DROP TABLE data_model_dependency_group');
        $this->addSql('DROP TABLE data_model_dependency_rule');
        $this->addSql('DROP INDEX UNIQ_B9356A66EA0F708D ON data_model_module');
        $this->addSql('ALTER TABLE data_model_module DROP dependencies, DROP is_dependent');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE distribution_rdf_mappings CHANGE data_model_version data_model_version CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\'');
    }
}
