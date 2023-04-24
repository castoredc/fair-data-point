<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230424223151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_268B9C9D989D9B62 ON agent (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1B2C3247989D9B62 ON catalog (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B7A041D0989D9B62 ON dataset (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A4483781989D9B62 ON distribution (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E67F9749989D9B62 ON study (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_268B9C9D989D9B62 ON agent');
        $this->addSql('DROP INDEX UNIQ_1B2C3247989D9B62 ON catalog');
        $this->addSql('DROP INDEX UNIQ_B7A041D0989D9B62 ON dataset');
        $this->addSql('DROP INDEX UNIQ_A4483781989D9B62 ON distribution');
        $this->addSql('DROP INDEX UNIQ_E67F9749989D9B62 ON study');
    }
}
