<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200610193810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE metadata (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', language VARCHAR(190) DEFAULT NULL, license VARCHAR(190) DEFAULT NULL, keyword CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, version VARCHAR(255) NOT NULL COMMENT \'(DC2Type:version)\', landing_page VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4F1434142B36786B (title), UNIQUE INDEX UNIQ_4F1434146DE44026 (description), INDEX IDX_4F143414D4DB71B5 (language), INDEX IDX_4F1434145768F419 (license), UNIQUE INDEX UNIQ_4F1434145A93713B (keyword), INDEX IDX_4F143414DE12AB56 (created_by), INDEX IDX_4F14341416FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dataset_publishers (metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_A1D6EA74DC9EE959 (metadata_id), INDEX IDX_A1D6EA743414710B (agent_id), PRIMARY KEY(metadata_id, agent_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dataset_contacts (metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_ACD423FADC9EE959 (metadata_id), INDEX IDX_ACD423FA3414710B (agent_id), PRIMARY KEY(metadata_id, agent_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_catalog (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', catalog CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', homepage VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\', logo VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\', INDEX IDX_895DE2BF1B2C3247 (catalog), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_dataset (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dataset CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_25D19128B7A041D0 (dataset), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_distribution (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_EC1C7F7BA4483781 (distribution), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F1434142B36786B FOREIGN KEY (title) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F1434146DE44026 FOREIGN KEY (description) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F143414D4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F1434145768F419 FOREIGN KEY (license) REFERENCES license (slug)');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F1434145A93713B FOREIGN KEY (keyword) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F143414DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F14341416FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dataset_publishers ADD CONSTRAINT FK_A1D6EA74DC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dataset_publishers ADD CONSTRAINT FK_A1D6EA743414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dataset_contacts ADD CONSTRAINT FK_ACD423FADC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dataset_contacts ADD CONSTRAINT FK_ACD423FA3414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_catalog ADD CONSTRAINT FK_895DE2BF1B2C3247 FOREIGN KEY (catalog) REFERENCES catalog (id)');
        $this->addSql('ALTER TABLE metadata_catalog ADD CONSTRAINT FK_895DE2BFBF396750 FOREIGN KEY (id) REFERENCES metadata (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_dataset ADD CONSTRAINT FK_25D19128B7A041D0 FOREIGN KEY (dataset) REFERENCES dataset (id)');
        $this->addSql('ALTER TABLE metadata_dataset ADD CONSTRAINT FK_25D19128BF396750 FOREIGN KEY (id) REFERENCES metadata (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_distribution ADD CONSTRAINT FK_EC1C7F7BA4483781 FOREIGN KEY (distribution) REFERENCES distribution (id)');
        $this->addSql('ALTER TABLE metadata_distribution ADD CONSTRAINT FK_EC1C7F7BBF396750 FOREIGN KEY (id) REFERENCES metadata (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32472B36786B');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32475768F419');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32476DE44026');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C3247D4DB71B5');
        $this->addSql('DROP INDEX UNIQ_1B2C32472B36786B ON catalog');
        $this->addSql('DROP INDEX UNIQ_1B2C32476DE44026 ON catalog');
        $this->addSql('DROP INDEX IDX_1B2C32475768F419 ON catalog');
        $this->addSql('DROP INDEX IDX_1B2C3247D4DB71B5 ON catalog');
        $this->addSql('ALTER TABLE catalog ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\', DROP title, DROP description, DROP language, DROP license, DROP version, DROP homepage, DROP logo, DROP created, CHANGE updated updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D05768F419');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D05A93713B');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D0D4DB71B5');
        $this->addSql('DROP INDEX UNIQ_B7A041D05A93713B ON dataset');
        $this->addSql('DROP INDEX IDX_B7A041D05768F419 ON dataset');
        $this->addSql('DROP INDEX IDX_B7A041D0D4DB71B5 ON dataset');
        $this->addSql('ALTER TABLE dataset DROP language, DROP license, DROP keyword, DROP landing_page, CHANGE created_at created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837812B36786B');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837815768F419');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837816DE44026');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A4483781D4DB71B5');
        $this->addSql('DROP INDEX UNIQ_A44837812B36786B ON distribution');
        $this->addSql('DROP INDEX UNIQ_A44837816DE44026 ON distribution');
        $this->addSql('DROP INDEX IDX_A44837815768F419 ON distribution');
        $this->addSql('DROP INDEX IDX_A4483781D4DB71B5 ON distribution');
        $this->addSql('ALTER TABLE distribution ADD created_at DATETIME NOT NULL  DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\', DROP title, DROP description, DROP language, DROP license, DROP version, DROP created, CHANGE updated updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE metadata_study ADD version VARCHAR(255) NOT NULL COMMENT \'(DC2Type:version)\', ADD created_at DATETIME NOT NULL DEFAULT NOW()  COMMENT \'(DC2Type:datetime_immutable)\', DROP created, CHANGE study_id study_id VARCHAR(190) NOT NULL, CHANGE updated updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dataset_publishers DROP FOREIGN KEY FK_A1D6EA74DC9EE959');
        $this->addSql('ALTER TABLE dataset_contacts DROP FOREIGN KEY FK_ACD423FADC9EE959');
        $this->addSql('ALTER TABLE metadata_catalog DROP FOREIGN KEY FK_895DE2BFBF396750');
        $this->addSql('ALTER TABLE metadata_dataset DROP FOREIGN KEY FK_25D19128BF396750');
        $this->addSql('ALTER TABLE metadata_distribution DROP FOREIGN KEY FK_EC1C7F7BBF396750');
        $this->addSql('DROP TABLE metadata');
        $this->addSql('DROP TABLE dataset_publishers');
        $this->addSql('DROP TABLE dataset_contacts');
        $this->addSql('DROP TABLE metadata_catalog');
        $this->addSql('DROP TABLE metadata_dataset');
        $this->addSql('DROP TABLE metadata_distribution');
        $this->addSql('ALTER TABLE catalog ADD title CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', ADD description CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', ADD language VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, ADD license VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, ADD version VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, ADD homepage VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:iri)\', ADD logo VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:iri)\', ADD created DATETIME NOT NULL, DROP created_at, CHANGE updated_at updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32472B36786B FOREIGN KEY (title) REFERENCES text_localized (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32475768F419 FOREIGN KEY (license) REFERENCES license (slug) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32476DE44026 FOREIGN KEY (description) REFERENCES text_localized (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C3247D4DB71B5 FOREIGN KEY (language) REFERENCES language (code) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1B2C32472B36786B ON catalog (title)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1B2C32476DE44026 ON catalog (description)');
        $this->addSql('CREATE INDEX IDX_1B2C32475768F419 ON catalog (license)');
        $this->addSql('CREATE INDEX IDX_1B2C3247D4DB71B5 ON catalog (language)');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE dataset ADD language VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, ADD license VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, ADD keyword CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', ADD landing_page VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:iri)\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D05768F419 FOREIGN KEY (license) REFERENCES license (slug) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D05A93713B FOREIGN KEY (keyword) REFERENCES text_localized (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D0D4DB71B5 FOREIGN KEY (language) REFERENCES language (code) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B7A041D05A93713B ON dataset (keyword)');
        $this->addSql('CREATE INDEX IDX_B7A041D05768F419 ON dataset (license)');
        $this->addSql('CREATE INDEX IDX_B7A041D0D4DB71B5 ON dataset (language)');
        $this->addSql('ALTER TABLE distribution ADD title CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', ADD description CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', ADD language VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, ADD license VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, ADD version VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, ADD created DATETIME NOT NULL, DROP created_at, CHANGE updated_at updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837812B36786B FOREIGN KEY (title) REFERENCES text_localized (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837815768F419 FOREIGN KEY (license) REFERENCES license (slug) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837816DE44026 FOREIGN KEY (description) REFERENCES text_localized (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A4483781D4DB71B5 FOREIGN KEY (language) REFERENCES language (code) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A44837812B36786B ON distribution (title)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A44837816DE44026 ON distribution (description)');
        $this->addSql('CREATE INDEX IDX_A44837815768F419 ON distribution (license)');
        $this->addSql('CREATE INDEX IDX_A4483781D4DB71B5 ON distribution (language)');
        $this->addSql('ALTER TABLE metadata_study ADD created DATETIME NOT NULL, DROP version, DROP created_at, CHANGE study_id study_id VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE updated_at updated DATETIME DEFAULT NULL');
    }
}
