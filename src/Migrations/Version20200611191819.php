<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200611191819 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function preUp(Schema $schema): void
    {
        parent::preUp($schema);
        $this->setForeignKeyChecks(false);
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema): void
    {
        parent::postUp($schema);
        $this->setForeignKeyChecks(true);
    }

    /**
     * @param Schema $schema
     */
    public function preDown(Schema $schema): void
    {
        parent::preDown($schema);
        $this->setForeignKeyChecks(false);
    }

    /**
     * @param Schema $schema
     */
    public function postDown(Schema $schema): void
    {
        parent::postDown($schema);
        $this->setForeignKeyChecks(true);
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE study_castor (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', server INT DEFAULT NULL, INDEX IDX_4DB9B2045A6DD5F6 (server), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE study_castor ADD CONSTRAINT FK_4DB9B2045A6DD5F6 FOREIGN KEY (server) REFERENCES castor_server (id)');
        $this->addSql('ALTER TABLE study_castor ADD CONSTRAINT FK_4DB9B204BF396750 FOREIGN KEY (id) REFERENCES study (id) ON DELETE CASCADE');

        $this->addSql('INSERT INTO study_castor (id, server) SELECT id, server from study');

        $this->addSql('ALTER TABLE castor_entity DROP FOREIGN KEY FK_90006568E7B003E9');
        $this->addSql('ALTER TABLE castor_entity CHANGE study_id study_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE castor_entity ADD CONSTRAINT FK_90006568E7B003E9 FOREIGN KEY (study_id) REFERENCES study_castor (id)');
        $this->addSql('ALTER TABLE study DROP FOREIGN KEY FK_E67F97495A6DD5F6');
        $this->addSql('DROP INDEX IDX_E67F97495A6DD5F6 ON study');
        $this->addSql('ALTER TABLE study ADD source_id VARCHAR(255) DEFAULT NULL, ADD source VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:StudySource)\', ADD type VARCHAR(255) NOT NULL, DROP server, CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE dataset CHANGE study_id study_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study CHANGE study_id study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');

        $this->addSql('UPDATE study SET source_id = id');
        $this->addSql('UPDATE study SET source = "castor"');
        $this->addSql('UPDATE study SET type = "castor"');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE castor_entity DROP FOREIGN KEY FK_90006568E7B003E9');
        $this->addSql('DROP TABLE study_castor');
        $this->addSql('ALTER TABLE castor_entity DROP FOREIGN KEY FK_90006568E7B003E9');
        $this->addSql('ALTER TABLE castor_entity CHANGE study_id study_id VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE castor_entity ADD CONSTRAINT FK_90006568E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE dataset CHANGE study_id study_id VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE metadata_study CHANGE study_id study_id VARCHAR(190) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE study ADD server INT DEFAULT NULL, DROP source_id, DROP source, DROP type, CHANGE id id VARCHAR(190) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
    }

    /**
     * @param boolean $enabled
     */
    protected function setForeignKeyChecks($enabled)
    {
        $connection = $this->connection;
        $platform = $connection->getDatabasePlatform();

        if ($platform instanceof MySqlPlatform) {
            $connection->exec(sprintf('SET FOREIGN_KEY_CHECKS = %s;', (int)$enabled));
        }
    }
}
