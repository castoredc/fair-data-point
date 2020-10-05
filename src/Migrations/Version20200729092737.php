<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200729092737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE castor_institute (id VARCHAR(190) NOT NULL, study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', country VARCHAR(190) DEFAULT NULL, institute_name VARCHAR(1000) NOT NULL, abbreviation VARCHAR(1000) NOT NULL, code VARCHAR(3) DEFAULT NULL, INDEX IDX_737F0857E7B003E9 (study_id), INDEX IDX_737F08575373C966 (country), PRIMARY KEY(id, study_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE castor_record (record_id VARCHAR(190) NOT NULL, study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', institute_id VARCHAR(190) NOT NULL, INDEX IDX_51CBE91E7B003E9 (study_id), INDEX IDX_51CBE91697B0F4CE7B003E9 (institute_id, study_id), PRIMARY KEY(record_id, study_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE castor_institute ADD CONSTRAINT FK_737F0857E7B003E9 FOREIGN KEY (study_id) REFERENCES study_castor (id)');
        $this->addSql('ALTER TABLE castor_institute ADD CONSTRAINT FK_737F08575373C966 FOREIGN KEY (country) REFERENCES country (code)');
        $this->addSql('ALTER TABLE castor_record ADD CONSTRAINT FK_51CBE91E7B003E9 FOREIGN KEY (study_id) REFERENCES study_castor (id)');
        $this->addSql('ALTER TABLE castor_record ADD CONSTRAINT FK_51CBE91697B0F4CE7B003E9 FOREIGN KEY (institute_id, study_id) REFERENCES castor_institute (id, study_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE castor_record DROP FOREIGN KEY FK_51CBE91697B0F4CE7B003E9');
        $this->addSql('DROP TABLE castor_institute');
        $this->addSql('DROP TABLE castor_record');
    }
}
