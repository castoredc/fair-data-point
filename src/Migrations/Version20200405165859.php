<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200405165859 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
;
        $this->addSql('ALTER TABLE study ADD server INT DEFAULT NULL');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F97495A6DD5F6 FOREIGN KEY (server) REFERENCES castor_server (id)');
        $this->addSql('CREATE INDEX IDX_E67F97495A6DD5F6 ON study (server)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE study DROP FOREIGN KEY FK_E67F97495A6DD5F6');
        $this->addSql('DROP INDEX IDX_E67F97495A6DD5F6 ON study');
        $this->addSql('ALTER TABLE study DROP server');
    }
}
