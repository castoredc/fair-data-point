<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210205134054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE data_transformation_mapping_element (data_transformation_mapping_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', element_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_19FCE9EA83FD70F0 (data_transformation_mapping_id), INDEX IDX_19FCE9EA1F1F2A24 (element_id), PRIMARY KEY(data_transformation_mapping_id, element_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE data_transformation_mapping_element ADD CONSTRAINT FK_19FCE9EA83FD70F0 FOREIGN KEY (data_transformation_mapping_id) REFERENCES data_specification_mappings (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_transformation_mapping_element ADD CONSTRAINT FK_19FCE9EA1F1F2A24 FOREIGN KEY (element_id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_specification_mappings ADD syntax LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE data_transformation_mapping_element');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE data_specification_mappings DROP syntax');
    }
}
