<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190904144652 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fdp_publishers (fairdata_point_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', contact_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_C3450E8BB0D86935 (fairdata_point_id), INDEX IDX_C3450E8BE7A1254A (contact_id), PRIMARY KEY(fairdata_point_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', orcid VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fdp_publishers ADD CONSTRAINT FK_C3450E8BB0D86935 FOREIGN KEY (fairdata_point_id) REFERENCES fdp (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fdp_publishers ADD CONSTRAINT FK_C3450E8BE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176BF396750 FOREIGN KEY (id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalog CHANGE homepage homepage VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dataset CHANGE landing_page landing_page VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fdp ADD language VARCHAR(255) DEFAULT NULL, ADD license CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE title title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE description description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F2B36786B FOREIGN KEY (title) REFERENCES localized_text (id)');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F6DE44026 FOREIGN KEY (description) REFERENCES localized_text (id)');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11FD4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F5768F419 FOREIGN KEY (license) REFERENCES license (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E28FB11F2B36786B ON fdp (title)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E28FB11F6DE44026 ON fdp (description)');
        $this->addSql('CREATE INDEX IDX_E28FB11FD4DB71B5 ON fdp (language)');
        $this->addSql('CREATE INDEX IDX_E28FB11F5768F419 ON fdp (license)');
        $this->addSql('ALTER TABLE organization CHANGE homepage homepage VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE fdp_publishers');
        $this->addSql('DROP TABLE person');
        $this->addSql('ALTER TABLE catalog CHANGE homepage homepage VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE dataset CHANGE landing_page landing_page VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F2B36786B');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F6DE44026');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11FD4DB71B5');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F5768F419');
        $this->addSql('DROP INDEX UNIQ_E28FB11F2B36786B ON fdp');
        $this->addSql('DROP INDEX UNIQ_E28FB11F6DE44026 ON fdp');
        $this->addSql('DROP INDEX IDX_E28FB11FD4DB71B5 ON fdp');
        $this->addSql('DROP INDEX IDX_E28FB11F5768F419 ON fdp');
        $this->addSql('ALTER TABLE fdp DROP language, DROP license, CHANGE title title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE description description VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE organization CHANGE homepage homepage VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
