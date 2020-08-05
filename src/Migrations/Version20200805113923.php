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
final class Version20200805113923 extends AbstractMigration
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

        $this->addSql('CREATE TABLE castor_user (id VARCHAR(190) NOT NULL, user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', name_first VARCHAR(255) NOT NULL, name_middle VARCHAR(255) DEFAULT NULL, name_last VARCHAR(255) NOT NULL, email_address VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_50208F68A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE castor_user ADD CONSTRAINT FK_50208F68A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE study CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE catalog CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_version CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_module CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_prefix CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_triple CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE dataset CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE castor_record CHANGE created_on created_on DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_on updated_on DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE distribution_contents CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_dependency CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_node CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_predicate CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf_mappings CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE user ADD castor_user_id VARCHAR(190) DEFAULT NULL, DROP full_name, CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A7F0FBB4 FOREIGN KEY (castor_user_id) REFERENCES castor_user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A7F0FBB4 ON user (castor_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A7F0FBB4');
        $this->addSql('DROP TABLE castor_user');
        $this->addSql('ALTER TABLE castor_record CHANGE created_on created_on DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_on updated_on DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE catalog CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_dependency CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_module CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_predicate CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_prefix CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_triple CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_version CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE dataset CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE distribution CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE distribution_contents CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE distribution_rdf_mappings CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE metadata CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE metadata_study CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE study CHANGE created_by created_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_by updated_by VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('DROP INDEX UNIQ_8D93D649A7F0FBB4 ON user');
        $this->addSql('ALTER TABLE user ADD full_name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, DROP castor_user_id, CHANGE id id VARCHAR(190) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
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
