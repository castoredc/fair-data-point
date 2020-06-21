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
final class Version20200621115257 extends AbstractMigration
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
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE distribution CHANGE user_api user_api CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE user_api CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE distribution CHANGE user_api user_api VARCHAR(190) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE user_api CHANGE id id VARCHAR(190) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
    }

    protected function setForeignKeyChecks(bool $enabled): void
    {
        $connection = $this->connection;
        $platform = $connection->getDatabasePlatform();

        if (! ($platform instanceof MySqlPlatform)) {
            return;
        }

        $connection->exec(sprintf('SET FOREIGN_KEY_CHECKS = %s;', (int) $enabled));
    }
}
