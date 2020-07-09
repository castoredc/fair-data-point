<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200708073852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE distribution_contactpoint (distribution_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_495112716EB6DDB5 (distribution_id), INDEX IDX_495112713414710B (agent_id), PRIMARY KEY(distribution_id, agent_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metadata_publishers (metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_99C06534DC9EE959 (metadata_id), INDEX IDX_99C065343414710B (agent_id), PRIMARY KEY(metadata_id, agent_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE distribution_contactpoint ADD CONSTRAINT FK_495112716EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES distribution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_contactpoint ADD CONSTRAINT FK_495112713414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_publishers ADD CONSTRAINT FK_99C06534DC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metadata_publishers ADD CONSTRAINT FK_99C065343414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE dataset_publishers');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dataset_publishers (metadata_id CHAR(36) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', agent_id CHAR(36) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', INDEX IDX_A1D6EA743414710B (agent_id), INDEX IDX_A1D6EA74DC9EE959 (metadata_id), PRIMARY KEY(metadata_id, agent_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE dataset_publishers ADD CONSTRAINT FK_A1D6EA743414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dataset_publishers ADD CONSTRAINT FK_A1D6EA74DC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP TABLE distribution_contactpoint');
        $this->addSql('DROP TABLE metadata_publishers');
    }
}
