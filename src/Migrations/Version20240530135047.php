<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240530135047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metadata_model_node_parents (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', resource_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ResourceType)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_model_node_parents ADD CONSTRAINT FK_14E69908BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_model_node_parents DROP FOREIGN KEY FK_14E69908BF396750');
        $this->addSql('DROP TABLE metadata_model_node_parents');
    }
}
