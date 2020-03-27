<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200326085622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE catalog ADD accept_submissions TINYINT(1) NOT NULL, CHANGE homepage homepage VARCHAR(255) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D02B36786B');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D06DE44026');
        $this->addSql('DROP INDEX UNIQ_B7A041D02B36786B ON dataset');
        $this->addSql('DROP INDEX UNIQ_B7A041D06DE44026 ON dataset');
        $this->addSql('ALTER TABLE dataset DROP title, DROP description, DROP version, DROP issued, DROP modified, DROP logo, CHANGE landing_page landing_page VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE department CHANGE additional_information additional_information LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE fdp CHANGE iri iri VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE license CHANGE url url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE organization CHANGE homepage homepage VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE person CHANGE orcid orcid VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE metadata_study ADD logo VARCHAR(255) DEFAULT NULL, CHANGE study_type study_type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE catalog DROP accept_submissions, CHANGE homepage homepage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dataset ADD title CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD description CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', ADD version VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD issued DATETIME NOT NULL, ADD modified DATETIME NOT NULL, ADD logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE landing_page landing_page VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D02B36786B FOREIGN KEY (title) REFERENCES text_localized (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D06DE44026 FOREIGN KEY (description) REFERENCES text_localized (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B7A041D02B36786B ON dataset (title)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B7A041D06DE44026 ON dataset (description)');
        $this->addSql('ALTER TABLE department CHANGE additional_information additional_information LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fdp CHANGE iri iri VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE license CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE metadata_study DROP logo, CHANGE study_type study_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE organization CHANGE homepage homepage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE person CHANGE orcid orcid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
