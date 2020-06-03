<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200603123915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE castor_entity ADD parent VARCHAR(190) DEFAULT NULL');
        $this->addSql('ALTER TABLE castor_entity ADD CONSTRAINT FK_900065683D8E604F FOREIGN KEY (parent) REFERENCES castor_entity (id)');
        $this->addSql('CREATE INDEX IDX_900065683D8E604F ON castor_entity (parent)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE castor_entity DROP FOREIGN KEY FK_900065683D8E604F');
        $this->addSql('DROP INDEX IDX_900065683D8E604F ON castor_entity');
        $this->addSql('ALTER TABLE castor_entity DROP parent');
    }
}
