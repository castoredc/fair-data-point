<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200729114057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE log_generation_distribution (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', status VARCHAR(255) NOT NULL COMMENT \'(DC2Type:StudyType)\', errors JSON DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_85B347B0A4483781 (distribution), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_generation_distribution_record (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', record VARCHAR(190) NOT NULL, study CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', log CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', status VARCHAR(255) NOT NULL COMMENT \'(DC2Type:StudyType)\', errors JSON DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DDCF39AB9B349F91E67F9749 (record, study), INDEX IDX_DDCF39AB8F3F68C5 (log), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log_generation_distribution ADD CONSTRAINT FK_85B347B0A4483781 FOREIGN KEY (distribution) REFERENCES distribution_contents (id)');
        $this->addSql('ALTER TABLE log_generation_distribution_record ADD CONSTRAINT FK_DDCF39AB9B349F91E67F9749 FOREIGN KEY (record, study) REFERENCES castor_record (record_id, study_id)');
        $this->addSql('ALTER TABLE log_generation_distribution_record ADD CONSTRAINT FK_DDCF39AB8F3F68C5 FOREIGN KEY (log) REFERENCES log_generation_distribution (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE log_generation_distribution_record DROP FOREIGN KEY FK_DDCF39AB8F3F68C5');
        $this->addSql('DROP TABLE log_generation_distribution');
        $this->addSql('DROP TABLE log_generation_distribution_record');
    }
}
