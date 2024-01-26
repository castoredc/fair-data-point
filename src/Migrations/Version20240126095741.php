<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240126095741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metadata_model (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_module (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_node (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_node_external (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', iri VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:iri)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_node_literal (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', value VARCHAR(255) NOT NULL, data_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:XsdDataType)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_node_record (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_node_value (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', is_annotated_value TINYINT(1) NOT NULL, data_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:XsdDataType)\', is_repeated TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_predicate (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', metadata_model CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', iri VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_E5276546DE12AB56 (created_by), INDEX IDX_E527654616FE72E1 (updated_by), INDEX IDX_E527654635F6CABB (metadata_model), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_prefix (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', prefix VARCHAR(255) NOT NULL, uri VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_F802EAEBDE12AB56 (created_by), INDEX IDX_F802EAEB16FE72E1 (updated_by), INDEX IDX_F802EAEB992ABE46 (data_model), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_triple (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', subject CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', predicate CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', object CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_2F9F501BFBCE3E7A (subject), INDEX IDX_2F9F501B301BAA7B (predicate), INDEX IDX_2F9F501BA8ADABEC (object), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_model_version (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metadata_model ADD CONSTRAINT FK_35F6CABBBF396750 FOREIGN KEY (id) REFERENCES data_specification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_module ADD CONSTRAINT FK_67974A4DBF396750 FOREIGN KEY (id) REFERENCES data_specification_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_node ADD CONSTRAINT FK_B5C562EDBF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_node_external ADD CONSTRAINT FK_FB071748BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_node_literal ADD CONSTRAINT FK_780AA98ABF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_node_record ADD CONSTRAINT FK_4E15C897BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_node_value ADD CONSTRAINT FK_5C61BA16BF396750 FOREIGN KEY (id) REFERENCES data_specification_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_predicate ADD CONSTRAINT FK_E5276546DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_predicate ADD CONSTRAINT FK_E527654616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_predicate ADD CONSTRAINT FK_E527654635F6CABB FOREIGN KEY (metadata_model) REFERENCES metadata_model_version (id)');
        $this->addSql('ALTER TABLE metadata_model_prefix ADD CONSTRAINT FK_F802EAEBDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_prefix ADD CONSTRAINT FK_F802EAEB16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metadata_model_prefix ADD CONSTRAINT FK_F802EAEB992ABE46 FOREIGN KEY (data_model) REFERENCES metadata_model_version (id)');
        $this->addSql('ALTER TABLE metadata_model_triple ADD CONSTRAINT FK_2F9F501BFBCE3E7A FOREIGN KEY (subject) REFERENCES metadata_model_node (id)');
        $this->addSql('ALTER TABLE metadata_model_triple ADD CONSTRAINT FK_2F9F501B301BAA7B FOREIGN KEY (predicate) REFERENCES metadata_model_predicate (id)');
        $this->addSql('ALTER TABLE metadata_model_triple ADD CONSTRAINT FK_2F9F501BA8ADABEC FOREIGN KEY (object) REFERENCES metadata_model_node (id)');
        $this->addSql('ALTER TABLE metadata_model_triple ADD CONSTRAINT FK_2F9F501BBF396750 FOREIGN KEY (id) REFERENCES data_specification_elementgroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_model_version ADD CONSTRAINT FK_82AFF578BF396750 FOREIGN KEY (id) REFERENCES data_specification_version (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metadata_model DROP FOREIGN KEY FK_35F6CABBBF396750');
        $this->addSql('ALTER TABLE metadata_model_module DROP FOREIGN KEY FK_67974A4DBF396750');
        $this->addSql('ALTER TABLE metadata_model_node DROP FOREIGN KEY FK_B5C562EDBF396750');
        $this->addSql('ALTER TABLE metadata_model_node_external DROP FOREIGN KEY FK_FB071748BF396750');
        $this->addSql('ALTER TABLE metadata_model_node_literal DROP FOREIGN KEY FK_780AA98ABF396750');
        $this->addSql('ALTER TABLE metadata_model_node_record DROP FOREIGN KEY FK_4E15C897BF396750');
        $this->addSql('ALTER TABLE metadata_model_node_value DROP FOREIGN KEY FK_5C61BA16BF396750');
        $this->addSql('ALTER TABLE metadata_model_predicate DROP FOREIGN KEY FK_E5276546DE12AB56');
        $this->addSql('ALTER TABLE metadata_model_predicate DROP FOREIGN KEY FK_E527654616FE72E1');
        $this->addSql('ALTER TABLE metadata_model_predicate DROP FOREIGN KEY FK_E527654635F6CABB');
        $this->addSql('ALTER TABLE metadata_model_prefix DROP FOREIGN KEY FK_F802EAEBDE12AB56');
        $this->addSql('ALTER TABLE metadata_model_prefix DROP FOREIGN KEY FK_F802EAEB16FE72E1');
        $this->addSql('ALTER TABLE metadata_model_prefix DROP FOREIGN KEY FK_F802EAEB992ABE46');
        $this->addSql('ALTER TABLE metadata_model_triple DROP FOREIGN KEY FK_2F9F501BFBCE3E7A');
        $this->addSql('ALTER TABLE metadata_model_triple DROP FOREIGN KEY FK_2F9F501B301BAA7B');
        $this->addSql('ALTER TABLE metadata_model_triple DROP FOREIGN KEY FK_2F9F501BA8ADABEC');
        $this->addSql('ALTER TABLE metadata_model_triple DROP FOREIGN KEY FK_2F9F501BBF396750');
        $this->addSql('ALTER TABLE metadata_model_version DROP FOREIGN KEY FK_82AFF578BF396750');
        $this->addSql('DROP TABLE metadata_model');
        $this->addSql('DROP TABLE metadata_model_module');
        $this->addSql('DROP TABLE metadata_model_node');
        $this->addSql('DROP TABLE metadata_model_node_external');
        $this->addSql('DROP TABLE metadata_model_node_literal');
        $this->addSql('DROP TABLE metadata_model_node_record');
        $this->addSql('DROP TABLE metadata_model_node_value');
        $this->addSql('DROP TABLE metadata_model_predicate');
        $this->addSql('DROP TABLE metadata_model_prefix');
        $this->addSql('DROP TABLE metadata_model_triple');
        $this->addSql('DROP TABLE metadata_model_version');
    }
}
