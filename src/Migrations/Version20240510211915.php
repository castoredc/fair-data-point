<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240510211915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog ADD default_metadata_model_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32473F018586 FOREIGN KEY (default_metadata_model_id) REFERENCES metadata_model (id)');
        $this->addSql('CREATE INDEX IDX_1B2C32473F018586 ON catalog (default_metadata_model_id)');
        $this->addSql('ALTER TABLE dataset ADD default_metadata_model_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D03F018586 FOREIGN KEY (default_metadata_model_id) REFERENCES metadata_model (id)');
        $this->addSql('CREATE INDEX IDX_B7A041D03F018586 ON dataset (default_metadata_model_id)');
        $this->addSql('ALTER TABLE distribution ADD default_metadata_model_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837813F018586 FOREIGN KEY (default_metadata_model_id) REFERENCES metadata_model (id)');
        $this->addSql('CREATE INDEX IDX_A44837813F018586 ON distribution (default_metadata_model_id)');
        $this->addSql('ALTER TABLE fdp ADD default_metadata_model_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F3F018586 FOREIGN KEY (default_metadata_model_id) REFERENCES metadata_model (id)');
        $this->addSql('CREATE INDEX IDX_E28FB11F3F018586 ON fdp (default_metadata_model_id)');
        $this->addSql('ALTER TABLE metadata ADD metadata_model_version_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F143414130157DB FOREIGN KEY (metadata_model_version_id) REFERENCES metadata_model_version (id)');
        $this->addSql('CREATE INDEX IDX_4F143414130157DB ON metadata (metadata_model_version_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32473F018586');
        $this->addSql('DROP INDEX IDX_1B2C32473F018586 ON catalog');
        $this->addSql('ALTER TABLE catalog DROP default_metadata_model_id');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D03F018586');
        $this->addSql('DROP INDEX IDX_B7A041D03F018586 ON dataset');
        $this->addSql('ALTER TABLE dataset DROP default_metadata_model_id');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837813F018586');
        $this->addSql('DROP INDEX IDX_A44837813F018586 ON distribution');
        $this->addSql('ALTER TABLE distribution DROP default_metadata_model_id');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F3F018586');
        $this->addSql('DROP INDEX IDX_E28FB11F3F018586 ON fdp');
        $this->addSql('ALTER TABLE fdp DROP default_metadata_model_id');
        $this->addSql('ALTER TABLE metadata DROP FOREIGN KEY FK_4F143414130157DB');
        $this->addSql('DROP INDEX IDX_4F143414130157DB ON metadata');
        $this->addSql('ALTER TABLE metadata DROP metadata_model_version_id');
    }
}
