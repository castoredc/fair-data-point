<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200324214823 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE catalog CHANGE homepage homepage VARCHAR(255) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dataset CHANGE landing_page landing_page VARCHAR(255) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fdp CHANGE iri iri VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE license CHANGE url url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE organization CHANGE homepage homepage VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE person CHANGE orcid orcid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2BBDD68843');
        $this->addSql('DROP INDEX UNIQ_41C2F2BBDD68843 ON metadata_study');
        $this->addSql('ALTER TABLE metadata_study ADD study_type VARCHAR(255) NOT NULL, DROP type, CHANGE `condition` studied_condition CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2B9F6690FD FOREIGN KEY (studied_condition) REFERENCES text_coded (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_41C2F2B9F6690FD ON metadata_study (studied_condition)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE catalog CHANGE homepage homepage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dataset CHANGE landing_page landing_page VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fdp CHANGE iri iri VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE license CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2B9F6690FD');
        $this->addSql('DROP INDEX UNIQ_41C2F2B9F6690FD ON metadata_study');
        $this->addSql('ALTER TABLE metadata_study ADD type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP study_type, CHANGE studied_condition `condition` CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2BBDD68843 FOREIGN KEY (`condition`) REFERENCES text_coded (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_41C2F2BBDD68843 ON metadata_study (`condition`)');
        $this->addSql('ALTER TABLE organization CHANGE homepage homepage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE person CHANGE orcid orcid VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
