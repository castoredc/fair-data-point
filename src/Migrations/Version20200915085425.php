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
final class Version20200915085425 extends AbstractMigration
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
        $this->addSql('UPDATE person SET name_origin = "peer"');
        $this->addSql('UPDATE user SET person_id = uuid()');

        $this->addSql('INSERT INTO person (id, first_name, middle_name, last_name, email, user_id, name_origin)' . "\n" .
            'SELECT person_id as id, name_first as first_name, name_middle as middle_name, name_last as last_name, email_address as email, id as user_id, name_origin from user');

        $this->addSql('INSERT INTO agent (id, slug, `name`, dtype)' . "\n" .
            'SELECT person_id as id, LOWER(REPLACE(REPLACE(CONCAT_WS(" ", name_first, name_middle, name_last), "  ", " "), " ", "-")) as slug, REPLACE(CONCAT_WS(" ", name_first, name_middle, name_last), "  ", " ") AS `name`, "person" as dtype from user');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE user SET person_id = NULL');
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
