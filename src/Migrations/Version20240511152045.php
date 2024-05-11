<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240511152045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metadata_value (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', value_node_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', value LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_FF14E056DC9EE959 (metadata_id), INDEX IDX_FF14E056ADAFEAB7 (value_node_id), INDEX IDX_FF14E056DE12AB56 (created_by), INDEX IDX_FF14E05616FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_value ADD CONSTRAINT FK_FF14E056DC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata (id)');
        $this->addSql('ALTER TABLE metadata_value ADD CONSTRAINT FK_FF14E056ADAFEAB7 FOREIGN KEY (value_node_id) REFERENCES metadata_model_node_value (id)');
        $this->addSql('ALTER TABLE metadata_value ADD CONSTRAINT FK_FF14E056DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_value ADD CONSTRAINT FK_FF14E05616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_value DROP FOREIGN KEY FK_FF14E056DC9EE959');
        $this->addSql('ALTER TABLE metadata_value DROP FOREIGN KEY FK_FF14E056ADAFEAB7');
        $this->addSql('ALTER TABLE metadata_value DROP FOREIGN KEY FK_FF14E056DE12AB56');
        $this->addSql('ALTER TABLE metadata_value DROP FOREIGN KEY FK_FF14E05616FE72E1');
        $this->addSql('DROP TABLE metadata_value');
    }
}
