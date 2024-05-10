<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240510174041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE metadata_model_module ADD resource_types VARCHAR(255) DEFAULT NULL');

        $this->addSql('CREATE TABLE metadata_model_node_internal (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', slug VARCHAR(255) NOT NULL, is_repeated TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_model_node_internal ADD CONSTRAINT FK_A8A6ABF3BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE metadata_model_field (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', metadata_model CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', form_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', option_group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, orderNumber INT DEFAULT NULL, resource_types VARCHAR(255) DEFAULT NULL, field_type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:MetadataFieldType)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_83C8D40835F6CABB (metadata_model), INDEX IDX_83C8D4085FF69B7D (form_id), INDEX IDX_83C8D408DE23A8E3 (option_group_id), INDEX IDX_83C8D408DE12AB56 (created_by), INDEX IDX_83C8D40816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_form (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', metadata_model CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, orderNumber INT DEFAULT NULL, resource_types VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_623277E735F6CABB (metadata_model), INDEX IDX_623277E7DE12AB56 (created_by), INDEX IDX_623277E716FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D40835F6CABB FOREIGN KEY (metadata_model) REFERENCES metadata_model_version (id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D4085FF69B7D FOREIGN KEY (form_id) REFERENCES metadata_model_form (id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D408DE23A8E3 FOREIGN KEY (option_group_id) REFERENCES metadata_model_option_group (id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D408DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D40816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_form ADD CONSTRAINT FK_623277E735F6CABB FOREIGN KEY (metadata_model) REFERENCES metadata_model_version (id)');
        $this->addSql('ALTER TABLE metadata_model_form ADD CONSTRAINT FK_623277E7DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_form ADD CONSTRAINT FK_623277E716FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_node_value DROP field_type');

        $this->addSql('ALTER TABLE metadata_model_field ADD description VARCHAR(255) DEFAULT NULL, CHANGE field_type field_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:MetadataFieldType)\'');

        $this->addSql('ALTER TABLE metadata_model_field ADD node_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_model_field ADD CONSTRAINT FK_83C8D408460D9FD7 FOREIGN KEY (node_id) REFERENCES metadata_model_node_value (id)');
        $this->addSql('CREATE INDEX IDX_83C8D408460D9FD7 ON metadata_model_field (node_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D408460D9FD7');
        $this->addSql('DROP INDEX IDX_83C8D408460D9FD7 ON metadata_model_field');
        $this->addSql('ALTER TABLE metadata_model_field DROP node_id');

        $this->addSql('ALTER TABLE metadata_model_field DROP description, CHANGE field_type field_type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:MetadataFieldType)\'');

        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D40835F6CABB');
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D4085FF69B7D');
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D408DE23A8E3');
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D408DE12AB56');
        $this->addSql('ALTER TABLE metadata_model_field DROP FOREIGN KEY FK_83C8D40816FE72E1');
        $this->addSql('ALTER TABLE metadata_model_form DROP FOREIGN KEY FK_623277E735F6CABB');
        $this->addSql('ALTER TABLE metadata_model_form DROP FOREIGN KEY FK_623277E7DE12AB56');
        $this->addSql('ALTER TABLE metadata_model_form DROP FOREIGN KEY FK_623277E716FE72E1');
        $this->addSql('DROP TABLE metadata_model_field');
        $this->addSql('DROP TABLE metadata_model_form');
        $this->addSql('ALTER TABLE metadata_model_node_value ADD field_type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:MetadataFieldType)\'');


        $this->addSql('ALTER TABLE metadata_model_node_internal DROP FOREIGN KEY FK_A8A6ABF3BF396750');
        $this->addSql('DROP TABLE metadata_model_node_internal');

        $this->addSql('ALTER TABLE metadata_model_module DROP resource_types');
    }
}
