<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200611201834 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE catalogs_studies (catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_E1F354EBCC3C66FC (catalog_id), INDEX IDX_E1F354EBE7B003E9 (study_id), PRIMARY KEY(catalog_id, study_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE catalogs_studies ADD CONSTRAINT FK_E1F354EBCC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalogs_studies ADD CONSTRAINT FK_E1F354EBE7B003E9 FOREIGN KEY (study_id) REFERENCES study (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study ADD is_published TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE catalogs_studies');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE study DROP is_published');
    }
}
