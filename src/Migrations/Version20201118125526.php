<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function sprintf;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201118125526 extends AbstractMigration
{
    public function preUp(Schema $schema): void
    {
        parent::preUp($schema);
        $this->setForeignKeyChecks(false);
    }

    public function postUp(Schema $schema): void
    {
        parent::postUp($schema);
        $this->setForeignKeyChecks(true);
    }

    public function preDown(Schema $schema): void
    {
        parent::preDown($schema);
        $this->setForeignKeyChecks(false);
    }

    public function postDown(Schema $schema): void
    {
        parent::postDown($schema);
        $this->setForeignKeyChecks(true);
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('TRUNCATE TABLE data_dictionary');
        $this->addSql('TRUNCATE TABLE data_dictionary_group');
        $this->addSql('TRUNCATE TABLE data_dictionary_option_group');
        $this->addSql('TRUNCATE TABLE data_dictionary_option_option');
        $this->addSql('TRUNCATE TABLE data_dictionary_variable');
        $this->addSql('TRUNCATE TABLE data_dictionary_version');
        $this->addSql('TRUNCATE TABLE data_model');
        $this->addSql('TRUNCATE TABLE data_model_module');
        $this->addSql('TRUNCATE TABLE data_model_node');
        $this->addSql('TRUNCATE TABLE data_model_node_external');
        $this->addSql('TRUNCATE TABLE data_model_node_internal');
        $this->addSql('TRUNCATE TABLE data_model_node_literal');
        $this->addSql('TRUNCATE TABLE data_model_node_record');
        $this->addSql('TRUNCATE TABLE data_model_node_value');
        $this->addSql('TRUNCATE TABLE data_model_predicate');
        $this->addSql('TRUNCATE TABLE data_model_prefix');
        $this->addSql('TRUNCATE TABLE data_model_triple');
        $this->addSql('TRUNCATE TABLE data_model_version');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    protected function setForeignKeyChecks(bool $enabled): void
    {
        $connection = $this->connection;
        $platform = $connection->getDatabasePlatform();

        if (! ($platform instanceof MySqlPlatform)) {
            return;
        }

        $connection->executeStatement(sprintf('SET FOREIGN_KEY_CHECKS = %s;', (int) $enabled));
    }
}
