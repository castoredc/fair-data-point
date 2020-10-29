<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201029145825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metadata_fdp (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', fdp CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_9B30AE35E28FB11F (fdp), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_fdp ADD CONSTRAINT FK_9B30AE35E28FB11F FOREIGN KEY (fdp) REFERENCES fdp (id)');
        $this->addSql('ALTER TABLE metadata_fdp ADD CONSTRAINT FK_9B30AE35BF396750 FOREIGN KEY (id) REFERENCES metadata (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F2B36786B');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F5768F419');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11F6DE44026');
        $this->addSql('ALTER TABLE fdp DROP FOREIGN KEY FK_E28FB11FD4DB71B5');
        $this->addSql('DROP INDEX UNIQ_E28FB11F6DE44026 ON fdp');
        $this->addSql('DROP INDEX IDX_E28FB11F5768F419 ON fdp');
        $this->addSql('DROP INDEX UNIQ_E28FB11F2B36786B ON fdp');
        $this->addSql('DROP INDEX IDX_E28FB11FD4DB71B5 ON fdp');
        $this->addSql('ALTER TABLE fdp DROP title, DROP description, DROP language, DROP license, DROP version');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE metadata_fdp');
        $this->addSql('ALTER TABLE fdp ADD title CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', ADD description CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', ADD language VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, ADD license VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, ADD version VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F2B36786B FOREIGN KEY (title) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F5768F419 FOREIGN KEY (license) REFERENCES license (slug)');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11F6DE44026 FOREIGN KEY (description) REFERENCES text_localized (id)');
        $this->addSql('ALTER TABLE fdp ADD CONSTRAINT FK_E28FB11FD4DB71B5 FOREIGN KEY (language) REFERENCES language (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E28FB11F6DE44026 ON fdp (description)');
        $this->addSql('CREATE INDEX IDX_E28FB11F5768F419 ON fdp (license)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E28FB11F2B36786B ON fdp (title)');
        $this->addSql('CREATE INDEX IDX_E28FB11FD4DB71B5 ON fdp (language)');
    }
}
