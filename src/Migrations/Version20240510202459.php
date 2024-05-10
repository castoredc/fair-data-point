<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240510202459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_model_field DROP INDEX IDX_83C8D408460D9FD7, ADD UNIQUE INDEX UNIQ_83C8D408460D9FD7 (node_id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\', DROP resource_types, CHANGE form_id form_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE node_id node_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_model_form ADD resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\', DROP resource_types');
        $this->addSql('ALTER TABLE metadata_model_module ADD resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\', DROP resource_types');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_model_field DROP INDEX UNIQ_83C8D408460D9FD7, ADD INDEX IDX_83C8D408460D9FD7 (node_id)');
        $this->addSql('ALTER TABLE metadata_model_field ADD resource_types VARCHAR(255) DEFAULT NULL, DROP resource_type, CHANGE form_id form_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE node_id node_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_model_form ADD resource_types VARCHAR(255) DEFAULT NULL, DROP resource_type');
        $this->addSql('ALTER TABLE metadata_model_module ADD resource_types VARCHAR(255) DEFAULT NULL, DROP resource_type');
    }
}
