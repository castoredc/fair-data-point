<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720094035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metadata_study_centers (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', metadata CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', organization CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_4F7869194F143414 (metadata), INDEX IDX_4F786919C1EE637C (organization), INDEX IDX_4F786919DE12AB56 (created_by), INDEX IDX_4F78691916FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_study_centers_departments (participating_center_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', department_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_168E7C40D75DAFF6 (participating_center_id), INDEX IDX_168E7C40AE80F5DF (department_id), PRIMARY KEY(participating_center_id, department_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_study_team (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', metadata CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', person CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', is_contact TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_A6D5E8694F143414 (metadata), INDEX IDX_A6D5E86934DCD176 (person), INDEX IDX_A6D5E869DE12AB56 (created_by), INDEX IDX_A6D5E86916FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_study_centers ADD CONSTRAINT FK_4F7869194F143414 FOREIGN KEY (metadata) REFERENCES metadata_study (id)');
        $this->addSql('ALTER TABLE metadata_study_centers ADD CONSTRAINT FK_4F786919C1EE637C FOREIGN KEY (organization) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE metadata_study_centers ADD CONSTRAINT FK_4F786919DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_study_centers ADD CONSTRAINT FK_4F78691916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_study_centers_departments ADD CONSTRAINT FK_168E7C40D75DAFF6 FOREIGN KEY (participating_center_id) REFERENCES metadata_study_centers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_study_centers_departments ADD CONSTRAINT FK_168E7C40AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_study_team ADD CONSTRAINT FK_A6D5E8694F143414 FOREIGN KEY (metadata) REFERENCES metadata_study (id)');
        $this->addSql('ALTER TABLE metadata_study_team ADD CONSTRAINT FK_A6D5E86934DCD176 FOREIGN KEY (person) REFERENCES person (id)');
        $this->addSql('ALTER TABLE metadata_study_team ADD CONSTRAINT FK_A6D5E869DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_study_team ADD CONSTRAINT FK_A6D5E86916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_study_centers_departments DROP FOREIGN KEY FK_168E7C40D75DAFF6');
        $this->addSql('DROP TABLE metadata_study_centers');
        $this->addSql('DROP TABLE metadata_study_centers_departments');
        $this->addSql('DROP TABLE metadata_study_team');
    }
}
