<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314074132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metadata_model_option_group (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_option_group_option (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_model_option_group ADD CONSTRAINT FK_1B9538BDBF396750 FOREIGN KEY (id) REFERENCES data_dictionary_option_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_option_group_option ADD CONSTRAINT FK_94C0346DBF396750 FOREIGN KEY (id) REFERENCES data_dictionary_option_option (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_dictionary_option_group ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_dictionary_option_option ADD dtype VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE metadata_model_node_value ADD field_type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:MetadataFieldType)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_model_option_group DROP FOREIGN KEY FK_1B9538BDBF396750');
        $this->addSql('ALTER TABLE metadata_model_option_group_option DROP FOREIGN KEY FK_94C0346DBF396750');
        $this->addSql('DROP TABLE metadata_model_option_group');
        $this->addSql('DROP TABLE metadata_model_option_group_option');
        $this->addSql('ALTER TABLE data_dictionary_option_group DROP type');
        $this->addSql('ALTER TABLE data_dictionary_option_option DROP dtype');
        $this->addSql('ALTER TABLE metadata_model_node_value DROP field_type');
    }
}
