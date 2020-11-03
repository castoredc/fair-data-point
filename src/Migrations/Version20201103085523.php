<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201103085523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata DROP FOREIGN KEY FK_4F1434145A93713B');
        $this->addSql('DROP INDEX UNIQ_4F1434145A93713B ON metadata');
        $this->addSql('ALTER TABLE metadata DROP keyword');
        $this->addSql('ALTER TABLE metadata_dataset ADD keyword CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_dataset ADD CONSTRAINT FK_25D191285A93713B FOREIGN KEY (keyword) REFERENCES text_localized (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_25D191285A93713B ON metadata_dataset (keyword)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata ADD keyword CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F1434145A93713B FOREIGN KEY (keyword) REFERENCES text_localized (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F1434145A93713B ON metadata (keyword)');
        $this->addSql('ALTER TABLE metadata_dataset DROP FOREIGN KEY FK_25D191285A93713B');
        $this->addSql('DROP INDEX UNIQ_25D191285A93713B ON metadata_dataset');
        $this->addSql('ALTER TABLE metadata_dataset DROP keyword');
    }
}
