<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200406081545 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_api (id VARCHAR(190) NOT NULL, server INT DEFAULT NULL, email_address VARCHAR(255) NOT NULL, client_id VARCHAR(255) NOT NULL, client_secret VARCHAR(255) NOT NULL, INDEX IDX_4613B9845A6DD5F6 (server), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_api ADD CONSTRAINT FK_4613B9845A6DD5F6 FOREIGN KEY (server) REFERENCES castor_server (id)');
        $this->addSql('ALTER TABLE catalog ADD user_api VARCHAR(190) DEFAULT NULL');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C32474613B984 FOREIGN KEY (user_api) REFERENCES user_api (id)');
        $this->addSql('CREATE INDEX IDX_1B2C32474613B984 ON catalog (user_api)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C32474613B984');
        $this->addSql('DROP TABLE user_api');
        $this->addSql('DROP INDEX IDX_1B2C32474613B984 ON catalog');
        $this->addSql('ALTER TABLE catalog DROP user_api');
    }
}
