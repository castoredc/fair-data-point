<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200610154523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE study CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE dataset DROP INDEX UNIQ_B7A041D0E7B003E9, ADD INDEX IDX_B7A041D0E7B003E9 (study_id)');
        $this->addSql('ALTER TABLE dataset ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\', DROP created, CHANGE updated updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dataset DROP INDEX IDX_B7A041D0E7B003E9, ADD UNIQUE INDEX UNIQ_B7A041D0E7B003E9 (study_id)');
        $this->addSql('ALTER TABLE dataset ADD created DATETIME NOT NULL, DROP created_at, CHANGE updated_at updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE study CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
