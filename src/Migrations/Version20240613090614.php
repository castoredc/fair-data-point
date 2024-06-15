<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240613090614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2B16FE72E1');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2BDE12AB56');
        $this->addSql('DROP INDEX IDX_41C2F2BDE12AB56 ON metadata_study');
        $this->addSql('DROP INDEX IDX_41C2F2B16FE72E1 ON metadata_study');
//        $this->addSql('ALTER TABLE metadata_study DROP created_by, DROP updated_by, DROP updated_at, DROP version, DROP created_at');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2BBF396750 FOREIGN KEY (id) REFERENCES metadata (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study ADD default_metadata_model_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F97493F018586 FOREIGN KEY (default_metadata_model_id) REFERENCES metadata_model (id)');
        $this->addSql('CREATE INDEX IDX_E67F97493F018586 ON study (default_metadata_model_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2BBF396750');
//        $this->addSql('ALTER TABLE metadata_study ADD created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD updated_at DATETIME DEFAULT NULL, ADD version VARCHAR(255) NOT NULL COMMENT \'(DC2Type:version)\', ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
//        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2B16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
//        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2BDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
//        $this->addSql('CREATE INDEX IDX_41C2F2BDE12AB56 ON metadata_study (created_by)');
//        $this->addSql('CREATE INDEX IDX_41C2F2B16FE72E1 ON metadata_study (updated_by)');
        $this->addSql('ALTER TABLE study DROP FOREIGN KEY FK_E67F97493F018586');
        $this->addSql('DROP INDEX IDX_E67F97493F018586 ON study');
        $this->addSql('ALTER TABLE study DROP default_metadata_model_id');
    }
}
