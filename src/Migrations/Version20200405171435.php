<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200405171435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE study ADD created_by VARCHAR(190) DEFAULT NULL, ADD updated_by VARCHAR(190) DEFAULT NULL, ADD created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F9749DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F974916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E67F9749DE12AB56 ON study (created_by)');
        $this->addSql('CREATE INDEX IDX_E67F974916FE72E1 ON study (updated_by)');
        $this->addSql('ALTER TABLE metadata_study ADD created_by VARCHAR(190) DEFAULT NULL, ADD updated_by VARCHAR(190) DEFAULT NULL');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2BDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_study ADD CONSTRAINT FK_41C2F2B16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_41C2F2BDE12AB56 ON metadata_study (created_by)');
        $this->addSql('CREATE INDEX IDX_41C2F2B16FE72E1 ON metadata_study (updated_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2BDE12AB56');
        $this->addSql('ALTER TABLE metadata_study DROP FOREIGN KEY FK_41C2F2B16FE72E1');
        $this->addSql('DROP INDEX IDX_41C2F2BDE12AB56 ON metadata_study');
        $this->addSql('DROP INDEX IDX_41C2F2B16FE72E1 ON metadata_study');
        $this->addSql('ALTER TABLE metadata_study DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE study DROP FOREIGN KEY FK_E67F9749DE12AB56');
        $this->addSql('ALTER TABLE study DROP FOREIGN KEY FK_E67F974916FE72E1');
        $this->addSql('DROP INDEX IDX_E67F9749DE12AB56 ON study');
        $this->addSql('DROP INDEX IDX_E67F974916FE72E1 ON study');
        $this->addSql('ALTER TABLE study DROP created_by, DROP updated_by, DROP created, DROP updated');
    }
}
