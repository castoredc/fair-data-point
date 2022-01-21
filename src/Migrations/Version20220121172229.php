<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220121172229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE permission_catalog (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:PermissionType)\', INDEX IDX_2C3DD310A76ED395 (user_id), INDEX IDX_2C3DD310CC3C66FC (catalog_id), PRIMARY KEY(user_id, catalog_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission_dataset (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dataset_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:PermissionType)\', INDEX IDX_80B1A087A76ED395 (user_id), INDEX IDX_80B1A087D47C2D1B (dataset_id), PRIMARY KEY(user_id, dataset_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission_distribution (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', distribution_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:PermissionType)\', INDEX IDX_CD5B6B74A76ED395 (user_id), INDEX IDX_CD5B6B746EB6DDB5 (distribution_id), PRIMARY KEY(user_id, distribution_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE permission_catalog ADD CONSTRAINT FK_2C3DD310A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE permission_catalog ADD CONSTRAINT FK_2C3DD310CC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id)');
        $this->addSql('ALTER TABLE permission_dataset ADD CONSTRAINT FK_80B1A087A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE permission_dataset ADD CONSTRAINT FK_80B1A087D47C2D1B FOREIGN KEY (dataset_id) REFERENCES dataset (id)');
        $this->addSql('ALTER TABLE permission_distribution ADD CONSTRAINT FK_CD5B6B74A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE permission_distribution ADD CONSTRAINT FK_CD5B6B746EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES distribution (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE permission_catalog');
        $this->addSql('DROP TABLE permission_dataset');
        $this->addSql('DROP TABLE permission_distribution');
    }
}
