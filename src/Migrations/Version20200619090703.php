<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200619090703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32474613B984');
        $this->addSql('DROP INDEX IDX_1B2C32474613B984 ON catalog');
        $this->addSql('ALTER TABLE catalog DROP user_api');
        $this->addSql('ALTER TABLE distribution ADD user_api VARCHAR(190) DEFAULT NULL');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A44837814613B984 FOREIGN KEY (user_api) REFERENCES user_api (id)');
        $this->addSql('CREATE INDEX IDX_A44837814613B984 ON distribution (user_api)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE catalog ADD user_api VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32474613B984 FOREIGN KEY (user_api) REFERENCES user_api (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1B2C32474613B984 ON catalog (user_api)');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE data_type data_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A44837814613B984');
        $this->addSql('DROP INDEX IDX_A44837814613B984 ON distribution');
        $this->addSql('ALTER TABLE distribution DROP user_api');
    }
}
