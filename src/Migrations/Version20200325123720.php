<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200325123720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE department (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', organization CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', additional_information LONGTEXT NOT NULL, INDEX IDX_CD1DE18AC1EE637C (organization), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study_centers (study_metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_1258265391AB1465 (study_metadata_id), INDEX IDX_125826533414710B (agent_id), PRIMARY KEY(study_metadata_id, agent_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18AC1EE637C FOREIGN KEY (organization) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18ABF396750 FOREIGN KEY (id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_centers ADD CONSTRAINT FK_1258265391AB1465 FOREIGN KEY (study_metadata_id) REFERENCES metadata_study (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_centers ADD CONSTRAINT FK_125826533414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalog CHANGE homepage homepage VARCHAR(255) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dataset CHANGE landing_page landing_page VARCHAR(255) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fdp CHANGE iri iri VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE license CHANGE url url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE organization ADD country VARCHAR(190) DEFAULT NULL, ADD city VARCHAR(255) NOT NULL, CHANGE homepage homepage VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C5373C966 FOREIGN KEY (country) REFERENCES country (code)');
        $this->addSql('CREATE INDEX IDX_C1EE637C5373C966 ON organization (country)');
        $this->addSql('ALTER TABLE person CHANGE orcid orcid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE metadata_study CHANGE study_type study_type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE study_centers');
        $this->addSql('ALTER TABLE catalog CHANGE homepage homepage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dataset CHANGE landing_page landing_page VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fdp CHANGE iri iri VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE license CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE metadata_study CHANGE study_type study_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637C5373C966');
        $this->addSql('DROP INDEX IDX_C1EE637C5373C966 ON organization');
        $this->addSql('ALTER TABLE organization DROP country, DROP city, CHANGE homepage homepage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE person CHANGE orcid orcid VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
