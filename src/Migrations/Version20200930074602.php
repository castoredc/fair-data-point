<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200930074602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD study CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf_mappings ADD CONSTRAINT FK_DA9D49EFE67F9749 FOREIGN KEY (study) REFERENCES study (id)');
        $this->addSql('CREATE INDEX IDX_DA9D49EFE67F9749 ON distribution_rdf_mappings (study)');
        $this->addSql('ALTER TABLE distribution_rdf_mappings RENAME TO data_model_mappings');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_model_mappings RENAME TO distribution_rdf_mappings');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP FOREIGN KEY FK_DA9D49EFE67F9749');
        $this->addSql('DROP INDEX IDX_DA9D49EFE67F9749 ON distribution_rdf_mappings');
        $this->addSql('ALTER TABLE distribution_rdf_mappings DROP study');
    }
}
