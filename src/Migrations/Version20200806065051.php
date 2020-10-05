<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200806065051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE orcid_user (orcid VARCHAR(255) NOT NULL, user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D8886EB5A76ED395 (user_id), PRIMARY KEY(orcid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orcid_user ADD CONSTRAINT FK_D8886EB5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD orcid_user_id VARCHAR(255) DEFAULT NULL, CHANGE email_address email_address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BDC9B428 FOREIGN KEY (orcid_user_id) REFERENCES orcid_user (orcid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649BDC9B428 ON user (orcid_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BDC9B428');
        $this->addSql('DROP TABLE orcid_user');
        $this->addSql('DROP INDEX UNIQ_8D93D649BDC9B428 ON user');
        $this->addSql('ALTER TABLE user DROP orcid_user_id, CHANGE email_address email_address VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
    }
}
