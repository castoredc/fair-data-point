<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function sprintf;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200805114045 extends AbstractMigration
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
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO castor_user (id, user_id, name_first, name_middle, name_last, email_address) SELECT id, id, name_first, name_middle, name_last, email_address from user');

        $this->addSql('UPDATE user SET castor_user_id = id');
    }

    public function down(Schema $schema): void
    {
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
