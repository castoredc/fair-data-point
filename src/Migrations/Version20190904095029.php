<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190904095029 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE study (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catalog (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', language VARCHAR(255) DEFAULT NULL, license CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', fdp CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', slug VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, issued DATETIME NOT NULL, modified DATETIME NOT NULL, homepage VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1B2C32472B36786B (title), UNIQUE INDEX UNIQ_1B2C32476DE44026 (description), INDEX IDX_1B2C3247D4DB71B5 (language), INDEX IDX_1B2C32475768F419 (license), INDEX IDX_1B2C3247E28FB11F (fdp), INDEX slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catalogs_publishers (catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', contact_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_EBE984D4CC3C66FC (catalog_id), INDEX IDX_EBE984D4E7A1254A (contact_id), PRIMARY KEY(catalog_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catalogs_datasets (catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dataset_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_1CF6F11DCC3C66FC (catalog_id), INDEX IDX_1CF6F11DD47C2D1B (dataset_id), PRIMARY KEY(catalog_id, dataset_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, dtype VARCHAR(255) NOT NULL, INDEX slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dataset (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', language VARCHAR(255) DEFAULT NULL, license CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', keyword CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', study_id VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, issued DATETIME NOT NULL, modified DATETIME NOT NULL, landing_page VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B7A041D02B36786B (title), UNIQUE INDEX UNIQ_B7A041D06DE44026 (description), INDEX IDX_B7A041D0D4DB71B5 (language), INDEX IDX_B7A041D05768F419 (license), UNIQUE INDEX UNIQ_B7A041D05A93713B (keyword), UNIQUE INDEX UNIQ_B7A041D0E7B003E9 (study_id), INDEX slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE datasets_publishers (dataset_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', contact_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_6AC743C2D47C2D1B (dataset_id), INDEX IDX_6AC743C2E7A1254A (contact_id), PRIMARY KEY(dataset_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE datasets_contactpoints (dataset_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', contact_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_317B24E6D47C2D1B (dataset_id), INDEX IDX_317B24E6E7A1254A (contact_id), PRIMARY KEY(dataset_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', language VARCHAR(255) DEFAULT NULL, license CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', dataset_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', slug VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, issued DATETIME NOT NULL, modified DATETIME NOT NULL, UNIQUE INDEX UNIQ_A44837812B36786B (title), UNIQUE INDEX UNIQ_A44837816DE44026 (description), INDEX IDX_A4483781D4DB71B5 (language), INDEX IDX_A44837815768F419 (license), INDEX IDX_A4483781D47C2D1B (dataset_id), INDEX slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distributions_publishers (distribution_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', contact_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_6AF6FE206EB6DDB5 (distribution_id), INDEX IDX_6AF6FE20E7A1254A (contact_id), PRIMARY KEY(distribution_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fdp (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', iri VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX iri (iri), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE license (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', slug VARCHAR(255) NOT NULL, short VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE localized_text (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE localized_text_item (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', parent CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', language VARCHAR(255) DEFAULT NULL, text VARCHAR(255) NOT NULL, INDEX IDX_B4CBD5BE3D8E604F (parent), INDEX IDX_B4CBD5BED4DB71B5 (language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', homepage VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32472B36786B FOREIGN KEY (title) REFERENCES localized_text (id)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32476DE44026 FOREIGN KEY (description) REFERENCES localized_text (id)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C3247D4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32475768F419 FOREIGN KEY (license) REFERENCES license (id)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C3247E28FB11F FOREIGN KEY (fdp) REFERENCES fdp (id)');
        $this->addSql('ALTER TABLE catalogs_publishers ADD CONSTRAINT FK_EBE984D4CC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalogs_publishers ADD CONSTRAINT FK_EBE984D4E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalogs_datasets ADD CONSTRAINT FK_1CF6F11DCC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalogs_datasets ADD CONSTRAINT FK_1CF6F11DD47C2D1B FOREIGN KEY (dataset_id) REFERENCES dataset (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D02B36786B FOREIGN KEY (title) REFERENCES localized_text (id)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D06DE44026 FOREIGN KEY (description) REFERENCES localized_text (id)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D0D4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D05768F419 FOREIGN KEY (license) REFERENCES license (id)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D05A93713B FOREIGN KEY (keyword) REFERENCES localized_text (id)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D0E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id)');
        $this->addSql('ALTER TABLE datasets_publishers ADD CONSTRAINT FK_6AC743C2D47C2D1B FOREIGN KEY (dataset_id) REFERENCES dataset (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE datasets_publishers ADD CONSTRAINT FK_6AC743C2E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE datasets_contactpoints ADD CONSTRAINT FK_317B24E6D47C2D1B FOREIGN KEY (dataset_id) REFERENCES dataset (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE datasets_contactpoints ADD CONSTRAINT FK_317B24E6E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837812B36786B FOREIGN KEY (title) REFERENCES localized_text (id)');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837816DE44026 FOREIGN KEY (description) REFERENCES localized_text (id)');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A4483781D4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837815768F419 FOREIGN KEY (license) REFERENCES license (id)');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A4483781D47C2D1B FOREIGN KEY (dataset_id) REFERENCES dataset (id)');
        $this->addSql('ALTER TABLE distributions_publishers ADD CONSTRAINT FK_6AF6FE206EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES distribution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distributions_publishers ADD CONSTRAINT FK_6AF6FE20E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE localized_text_item ADD CONSTRAINT FK_B4CBD5BE3D8E604F FOREIGN KEY (parent) REFERENCES localized_text (id)');
        $this->addSql('ALTER TABLE localized_text_item ADD CONSTRAINT FK_B4CBD5BED4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637CBF396750 FOREIGN KEY (id) REFERENCES contact (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D0E7B003E9');
        $this->addSql('ALTER TABLE catalogs_publishers DROP FOREIGN KEY FK_EBE984D4CC3C66FC');
        $this->addSql('ALTER TABLE catalogs_datasets DROP FOREIGN KEY FK_1CF6F11DCC3C66FC');
        $this->addSql('ALTER TABLE catalogs_publishers DROP FOREIGN KEY FK_EBE984D4E7A1254A');
        $this->addSql('ALTER TABLE datasets_publishers DROP FOREIGN KEY FK_6AC743C2E7A1254A');
        $this->addSql('ALTER TABLE datasets_contactpoints DROP FOREIGN KEY FK_317B24E6E7A1254A');
        $this->addSql('ALTER TABLE distributions_publishers DROP FOREIGN KEY FK_6AF6FE20E7A1254A');
        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637CBF396750');
        $this->addSql('ALTER TABLE catalogs_datasets DROP FOREIGN KEY FK_1CF6F11DD47C2D1B');
        $this->addSql('ALTER TABLE datasets_publishers DROP FOREIGN KEY FK_6AC743C2D47C2D1B');
        $this->addSql('ALTER TABLE datasets_contactpoints DROP FOREIGN KEY FK_317B24E6D47C2D1B');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A4483781D47C2D1B');
        $this->addSql('ALTER TABLE distributions_publishers DROP FOREIGN KEY FK_6AF6FE206EB6DDB5');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C3247E28FB11F');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C3247D4DB71B5');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D0D4DB71B5');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A4483781D4DB71B5');
        $this->addSql('ALTER TABLE localized_text_item DROP FOREIGN KEY FK_B4CBD5BED4DB71B5');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32475768F419');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D05768F419');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837815768F419');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32472B36786B');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32476DE44026');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D02B36786B');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D06DE44026');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D05A93713B');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837812B36786B');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837816DE44026');
        $this->addSql('ALTER TABLE localized_text_item DROP FOREIGN KEY FK_B4CBD5BE3D8E604F');
        $this->addSql('DROP TABLE study');
        $this->addSql('DROP TABLE catalog');
        $this->addSql('DROP TABLE catalogs_publishers');
        $this->addSql('DROP TABLE catalogs_datasets');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE dataset');
        $this->addSql('DROP TABLE datasets_publishers');
        $this->addSql('DROP TABLE datasets_contactpoints');
        $this->addSql('DROP TABLE distribution');
        $this->addSql('DROP TABLE distributions_publishers');
        $this->addSql('DROP TABLE fdp');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE license');
        $this->addSql('DROP TABLE localized_text');
        $this->addSql('DROP TABLE localized_text_item');
        $this->addSql('DROP TABLE organization');
    }
}
