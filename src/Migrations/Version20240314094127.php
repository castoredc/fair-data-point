<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314094127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_dictionary_option_group ADD version CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_dictionary_option_group ADD CONSTRAINT FK_EAE76F60BF1CD3C3 FOREIGN KEY (version) REFERENCES data_specification_version (id)');
        $this->addSql('CREATE INDEX IDX_EAE76F60BF1CD3C3 ON data_dictionary_option_group (version)');
        $this->addSql('ALTER TABLE data_dictionary_option_option ADD orderNumber INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_dictionary_option_group DROP FOREIGN KEY FK_EAE76F60BF1CD3C3');
        $this->addSql('DROP INDEX IDX_EAE76F60BF1CD3C3 ON data_dictionary_option_group');
        $this->addSql('ALTER TABLE data_dictionary_option_group DROP version');
        $this->addSql('ALTER TABLE data_dictionary_option_option DROP orderNumber');
    }
}
