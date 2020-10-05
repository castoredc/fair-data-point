<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200930075815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE data_model_mappings SET study = (SELECT study.id FROM distribution_contents JOIN distribution on distribution_contents.distribution = distribution.id JOIN dataset on distribution.dataset_id = dataset.id JOIN study on dataset.study_id = study.id WHERE distribution_contents.id = data_model_mappings.distribution)');

        $this->addSql('ALTER TABLE data_model_mappings DROP FOREIGN KEY FK_DA9D49EFA4483781');
        $this->addSql('DROP INDEX IDX_DA9D49EFA4483781 ON data_model_mappings');
        $this->addSql('ALTER TABLE data_model_mappings DROP distribution');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_da9d49efe67f9749 TO IDX_7BA9938E67F9749');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_da9d49efe284468 TO IDX_7BA9938E284468');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_da9d49ef2ecdae18 TO IDX_7BA99382ECDAE18');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_da9d49efde12ab56 TO IDX_7BA9938DE12AB56');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_da9d49ef16fe72e1 TO IDX_7BA993816FE72E1');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_da9d49ef857fe845 TO IDX_7BA9938857FE845');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_da9d49efc242628 TO IDX_7BA9938C242628');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_model_mappings ADD distribution CHAR(36) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_mappings ADD CONSTRAINT FK_DA9D49EFA4483781 FOREIGN KEY (distribution) REFERENCES distribution_rdf (id)');
        $this->addSql('CREATE INDEX IDX_DA9D49EFA4483781 ON data_model_mappings (distribution)');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_7ba9938857fe845 TO IDX_DA9D49EF857FE845');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_7ba9938de12ab56 TO IDX_DA9D49EFDE12AB56');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_7ba99382ecdae18 TO IDX_DA9D49EF2ECDAE18');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_7ba9938e67f9749 TO IDX_DA9D49EFE67F9749');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_7ba9938e284468 TO IDX_DA9D49EFE284468');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_7ba993816fe72e1 TO IDX_DA9D49EF16FE72E1');
        $this->addSql('ALTER TABLE data_model_mappings RENAME INDEX idx_7ba9938c242628 TO IDX_DA9D49EFC242628');
    }
}
