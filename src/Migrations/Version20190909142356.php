<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190909142356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agent ADD fdp CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9DE28FB11F FOREIGN KEY (fdp) REFERENCES fdp (id)');
        $this->addSql('CREATE INDEX IDX_268B9C9DE28FB11F ON agent (fdp)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9DE28FB11F');
        $this->addSql('DROP INDEX IDX_268B9C9DE28FB11F ON agent');
        $this->addSql('ALTER TABLE agent DROP fdp');
    }
}
