<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230424232734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog ADD is_archived TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE dataset ADD is_archived TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE distribution ADD is_archived TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE fdp ADD is_archived TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE study ADD is_archived TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog DROP is_archived');
        $this->addSql('ALTER TABLE dataset DROP is_archived');
        $this->addSql('ALTER TABLE distribution DROP is_archived');
        $this->addSql('ALTER TABLE fdp DROP is_archived');
        $this->addSql('ALTER TABLE study DROP is_archived');
    }
}
