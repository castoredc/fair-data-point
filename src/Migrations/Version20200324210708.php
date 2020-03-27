<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200324210708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE study (id VARCHAR(190) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE agent (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, dtype VARCHAR(255) NOT NULL, INDEX slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catalog (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', language VARCHAR(190) DEFAULT NULL, license VARCHAR(190) DEFAULT NULL, fdp CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', slug VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, issued DATETIME NOT NULL, modified DATETIME NOT NULL, homepage VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1B2C32472B36786B (title), UNIQUE INDEX UNIQ_1B2C32476DE44026 (description), INDEX IDX_1B2C3247D4DB71B5 (language), INDEX IDX_1B2C32475768F419 (license), INDEX IDX_1B2C3247E28FB11F (fdp), INDEX slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catalogs_datasets (catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dataset_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_1CF6F11DCC3C66FC (catalog_id), INDEX IDX_1CF6F11DD47C2D1B (dataset_id), PRIMARY KEY(catalog_id, dataset_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dataset (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', language VARCHAR(190) DEFAULT NULL, license VARCHAR(190) DEFAULT NULL, keyword CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', study_id VARCHAR(190) DEFAULT NULL, slug VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, issued DATETIME NOT NULL, modified DATETIME NOT NULL, landing_page VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_B7A041D02B36786B (title), UNIQUE INDEX UNIQ_B7A041D06DE44026 (description), INDEX IDX_B7A041D0D4DB71B5 (language), INDEX IDX_B7A041D05768F419 (license), UNIQUE INDEX UNIQ_B7A041D05A93713B (keyword), UNIQUE INDEX UNIQ_B7A041D0E7B003E9 (study_id), INDEX slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', language VARCHAR(190) DEFAULT NULL, license VARCHAR(190) DEFAULT NULL, dataset_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', slug VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, issued DATETIME NOT NULL, modified DATETIME NOT NULL, access ENUM(\'1\', \'2\', \'3\') NOT NULL COMMENT \'(DC2Type:DistributionAccessType)\', dtype VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A44837812B36786B (title), UNIQUE INDEX UNIQ_A44837816DE44026 (description), INDEX IDX_A4483781D4DB71B5 (language), INDEX IDX_A44837815768F419 (license), INDEX IDX_A4483781D47C2D1B (dataset_id), INDEX slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution_rdf (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', prefix LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution_rdf_modules (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', distribution_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, `order` INT NOT NULL, twig LONGTEXT NOT NULL, INDEX IDX_95CA20636EB6DDB5 (distribution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fdp (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', language VARCHAR(190) DEFAULT NULL, license VARCHAR(190) DEFAULT NULL, iri VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E28FB11F2B36786B (title), UNIQUE INDEX UNIQ_E28FB11F6DE44026 (description), INDEX IDX_E28FB11FD4DB71B5 (language), INDEX IDX_E28FB11F5768F419 (license), INDEX iri (iri), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (code VARCHAR(190) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE license (slug VARCHAR(190) NOT NULL, url VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(slug)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE text_localized (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE text_localized_item (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', parent CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', language VARCHAR(190) DEFAULT NULL, text VARCHAR(255) NOT NULL, INDEX IDX_923CA1F03D8E604F (parent), INDEX IDX_923CA1F0D4DB71B5 (language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', homepage VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, orcid VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_study (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', study_id VARCHAR(190) DEFAULT NULL, `condition` CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', intervention CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', brief_name VARCHAR(255) NOT NULL, scientific_name LONGTEXT DEFAULT NULL, brief_summary LONGTEXT DEFAULT NULL, summary LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, estimated_enrollment INT NOT NULL, estimated_study_start_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', estimated_study_completion_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', study_completion_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, INDEX IDX_41C2F2BE7B003E9 (study_id), UNIQUE INDEX UNIQ_41C2F2BBDD68843 (`condition`), UNIQUE INDEX UNIQ_41C2F2BD11814AB (intervention), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE text_coded (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', text VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id VARCHAR(190) NOT NULL, full_name VARCHAR(255) NOT NULL, name_first VARCHAR(255) NOT NULL, name_middle VARCHAR(255) DEFAULT NULL, name_last VARCHAR(255) NOT NULL, email_address VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32472B36786B FOREIGN KEY (title) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32476DE44026 FOREIGN KEY (description) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C3247D4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32475768F419 FOREIGN KEY (license) REFERENCES license (slug)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C3247E28FB11F FOREIGN KEY (fdp) REFERENCES fdp (id)');
        $this->addSql('ALTER TABLE catalogs_datasets ADD CONSTRAINT FK_1CF6F11DCC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalogs_datasets ADD CONSTRAINT FK_1CF6F11DD47C2D1B FOREIGN KEY (dataset_id) REFERENCES dataset (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D02B36786B FOREIGN KEY (title) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D06DE44026 FOREIGN KEY (description) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D0D4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D05768F419 FOREIGN KEY (license) REFERENCES license (slug)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D05A93713B FOREIGN KEY (keyword) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D0E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id)');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837812B36786B FOREIGN KEY (title) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837816DE44026 FOREIGN KEY (description) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A4483781D4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837815768F419 FOREIGN KEY (license) REFERENCES license (slug)');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A4483781D47C2D1B FOREIGN KEY (dataset_id) REFERENCES dataset (id)');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AFBF396750 FOREIGN KEY (id) REFERENCES distribution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_rdf_modules ADD CONSTRAINT FK_95CA20636EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES distribution_rdf (id)');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F2B36786B FOREIGN KEY (title) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F6DE44026 FOREIGN KEY (description) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11FD4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F5768F419 FOREIGN KEY (license) REFERENCES license (slug)');
        $this->addSql('ALTER TABLE text_localized_item ADD CONSTRAINT FK_923CA1F03D8E604F FOREIGN KEY (parent) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE text_localized_item ADD CONSTRAINT FK_923CA1F0D4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637CBF396750 FOREIGN KEY (id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176BF396750 FOREIGN KEY (id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2BE7B003E9 FOREIGN KEY (study_id) REFERENCES study (id)');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2BBDD68843 FOREIGN KEY (`condition`) REFERENCES text_coded (id)');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2BD11814AB FOREIGN KEY (intervention) REFERENCES text_coded (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D0E7B003E9');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2BE7B003E9');
        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637CBF396750');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176BF396750');
        $this->addSql('ALTER TABLE catalogs_datasets DROP FOREIGN KEY FK_1CF6F11DCC3C66FC');
        $this->addSql('ALTER TABLE catalogs_datasets DROP FOREIGN KEY FK_1CF6F11DD47C2D1B');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A4483781D47C2D1B');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AFBF396750');
        $this->addSql('ALTER TABLE distribution_rdf_modules DROP FOREIGN KEY FK_95CA20636EB6DDB5');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C3247E28FB11F');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C3247D4DB71B5');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D0D4DB71B5');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A4483781D4DB71B5');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11FD4DB71B5');
        $this->addSql('ALTER TABLE text_localized_item DROP FOREIGN KEY FK_923CA1F0D4DB71B5');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32475768F419');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D05768F419');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837815768F419');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F5768F419');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32472B36786B');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32476DE44026');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D02B36786B');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D06DE44026');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D05A93713B');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837812B36786B');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837816DE44026');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F2B36786B');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F6DE44026');
        $this->addSql('ALTER TABLE text_localized_item DROP FOREIGN KEY FK_923CA1F03D8E604F');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2BBDD68843');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2BD11814AB');
        $this->addSql('DROP TABLE study');
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE catalog');
        $this->addSql('DROP TABLE catalogs_datasets');
        $this->addSql('DROP TABLE dataset');
        $this->addSql('DROP TABLE distribution');
        $this->addSql('DROP TABLE distribution_rdf');
        $this->addSql('DROP TABLE distribution_rdf_modules');
        $this->addSql('DROP TABLE fdp');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE license');
        $this->addSql('DROP TABLE text_localized');
        $this->addSql('DROP TABLE text_localized_item');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE metadata_study');
        $this->addSql('DROP TABLE text_coded');
        $this->addSql('DROP TABLE user');
    }
}
