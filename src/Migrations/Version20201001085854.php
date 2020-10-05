<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201001085854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE distribution_dependency_rule (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:DistributionContentsDependencyType)\', operator VARCHAR(255) NOT NULL COMMENT \'(DC2Type:DependencyOperatorType)\', value VARCHAR(255) NOT NULL, INDEX IDX_26D833A3857FE845 (node), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution_dependency (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_3B1E9E0AFE54D947 (group_id), INDEX IDX_3B1E9E0ADE12AB56 (created_by), INDEX IDX_3B1E9E0A16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution_dependency_group (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', combinator VARCHAR(255) NOT NULL COMMENT \'(DC2Type:DependencyCombinatorType)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE distribution_dependency_rule ADD CONSTRAINT FK_26D833A3857FE845 FOREIGN KEY (node) REFERENCES data_model_node_value (id)');
        $this->addSql('ALTER TABLE distribution_dependency_rule ADD CONSTRAINT FK_26D833A3BF396750 FOREIGN KEY (id) REFERENCES distribution_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_dependency ADD CONSTRAINT FK_3B1E9E0AFE54D947 FOREIGN KEY (group_id) REFERENCES distribution_dependency_group (id)');
        $this->addSql('ALTER TABLE distribution_dependency ADD CONSTRAINT FK_3B1E9E0ADE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE distribution_dependency ADD CONSTRAINT FK_3B1E9E0A16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE distribution_dependency_group ADD CONSTRAINT FK_B0AD3893BF396750 FOREIGN KEY (id) REFERENCES distribution_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_contents ADD dependencies CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_contents ADD CONSTRAINT FK_91757299EA0F708D FOREIGN KEY (dependencies) REFERENCES distribution_dependency_group (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_91757299EA0F708D ON distribution_contents (dependencies)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE distribution_dependency_rule DROP FOREIGN KEY FK_26D833A3BF396750');
        $this->addSql('ALTER TABLE distribution_dependency_group DROP FOREIGN KEY FK_B0AD3893BF396750');
        $this->addSql('ALTER TABLE distribution_contents DROP FOREIGN KEY FK_91757299EA0F708D');
        $this->addSql('ALTER TABLE distribution_dependency DROP FOREIGN KEY FK_3B1E9E0AFE54D947');
        $this->addSql('DROP TABLE distribution_dependency_rule');
        $this->addSql('DROP TABLE distribution_dependency');
        $this->addSql('DROP TABLE distribution_dependency_group');
        $this->addSql('DROP INDEX UNIQ_91757299EA0F708D ON distribution_contents');
        $this->addSql('ALTER TABLE distribution_contents DROP dependencies');
    }
}
