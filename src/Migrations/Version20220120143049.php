<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220120143049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE permission_data_specification (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_specification_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:PermissionType)\', INDEX IDX_426BAC0EA76ED395 (user_id), INDEX IDX_426BAC0E13281BD0 (data_specification_id), PRIMARY KEY(user_id, data_specification_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE permission_data_specification ADD CONSTRAINT FK_426BAC0EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE permission_data_specification ADD CONSTRAINT FK_426BAC0E13281BD0 FOREIGN KEY (data_specification_id) REFERENCES data_specification (id)');
        $this->addSql('ALTER TABLE data_specification ADD is_public TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE permission_data_specification');
        $this->addSql('ALTER TABLE data_specification DROP is_public');
    }
}
