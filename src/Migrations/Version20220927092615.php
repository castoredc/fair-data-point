<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220927092615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE permission_distribution_contents (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', distribution_contents_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:PermissionType)\', INDEX IDX_3650CA04A76ED395 (user_id), INDEX IDX_3650CA04A9E5D819 (distribution_contents_id), PRIMARY KEY(user_id, distribution_contents_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE permission_distribution_contents ADD CONSTRAINT FK_3650CA04A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE permission_distribution_contents ADD CONSTRAINT FK_3650CA04A9E5D819 FOREIGN KEY (distribution_contents_id) REFERENCES distribution_contents (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE permission_distribution_contents');
    }
}
