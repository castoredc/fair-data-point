<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201126150848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_dictionary_variable DROP FOREIGN KEY FK_DB31EFA2542BF9AD');
        $this->addSql('DROP INDEX IDX_DB31EFA2542BF9AD ON data_dictionary_variable');
        $this->addSql('ALTER TABLE data_dictionary_variable DROP option_group');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_specification_element ADD option_group CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_element ADD CONSTRAINT FK_CE19CB2D542BF9AD FOREIGN KEY (option_group) REFERENCES data_dictionary_option_group (id)');
        $this->addSql('CREATE INDEX IDX_CE19CB2D542BF9AD ON data_specification_element (option_group)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_dictionary_variable ADD option_group CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_dictionary_variable ADD CONSTRAINT FK_DB31EFA2542BF9AD FOREIGN KEY (option_group) REFERENCES data_dictionary_option_group (id)');
        $this->addSql('CREATE INDEX IDX_DB31EFA2542BF9AD ON data_dictionary_variable (option_group)');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_specification_element DROP FOREIGN KEY FK_CE19CB2D542BF9AD');
        $this->addSql('DROP INDEX IDX_CE19CB2D542BF9AD ON data_specification_element');
        $this->addSql('ALTER TABLE data_specification_element DROP option_group');
    }
}
