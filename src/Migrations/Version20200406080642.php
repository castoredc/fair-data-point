<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200406080642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE catalog CHANGE homepage homepage VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\', CHANGE logo logo VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\'');
        $this->addSql('CREATE INDEX slug ON catalog (slug)');
        $this->addSql('DROP INDEX slug ON study');
        $this->addSql('ALTER TABLE study CHANGE created created DATETIME NOT NULL');
        $this->addSql('CREATE INDEX slug ON study (slug)');
        $this->addSql('DROP INDEX slug ON dataset');
        $this->addSql('ALTER TABLE dataset CHANGE landing_page landing_page VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\'');
        $this->addSql('CREATE INDEX slug ON dataset (slug)');
        $this->addSql('ALTER TABLE metadata_study CHANGE study_type study_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:StudyType)\', CHANGE logo logo VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\', CHANGE recruitment_status recruitment_status VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:RecruitmentStatusType)\', CHANGE method_type method_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:MethodType)\'');
        $this->addSql('DROP INDEX slug ON distribution');
        $this->addSql('CREATE INDEX slug ON distribution (slug)');
        $this->addSql('ALTER TABLE castor_server CHANGE url url VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\'');
        $this->addSql('DROP INDEX slug ON agent');
        $this->addSql('CREATE INDEX slug ON agent (slug)');
        $this->addSql('ALTER TABLE person CHANGE orcid orcid VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\'');
        $this->addSql('ALTER TABLE organization CHANGE homepage homepage VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\'');
        $this->addSql('DROP INDEX iri ON fdp');
        $this->addSql('ALTER TABLE fdp CHANGE iri iri VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\'');
        $this->addSql('CREATE INDEX iri ON fdp (iri)');
        $this->addSql('ALTER TABLE license CHANGE url url VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX slug ON agent');
        $this->addSql('CREATE INDEX slug ON agent (slug(191))');
        $this->addSql('ALTER TABLE castor_server CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX slug ON catalog');
        $this->addSql('ALTER TABLE catalog CHANGE homepage homepage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE INDEX slug ON catalog (slug(191))');
        $this->addSql('DROP INDEX slug ON dataset');
        $this->addSql('ALTER TABLE dataset CHANGE landing_page landing_page VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE INDEX slug ON dataset (slug(191))');
        $this->addSql('DROP INDEX slug ON distribution');
        $this->addSql('CREATE INDEX slug ON distribution (slug(191))');
        $this->addSql('DROP INDEX iri ON fdp');
        $this->addSql('ALTER TABLE fdp CHANGE iri iri VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE INDEX iri ON fdp (iri(191))');
        $this->addSql('ALTER TABLE license CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE metadata_study CHANGE study_type study_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE method_type method_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE recruitment_status recruitment_status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE organization CHANGE homepage homepage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE person CHANGE orcid orcid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX slug ON study');
        $this->addSql('ALTER TABLE study CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX slug ON study (slug(191))');
    }
}
