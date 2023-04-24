<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321142554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE distribution ADD is_published TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE distribution_contents DROP access, CHANGE is_published is_public TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE distribution DROP is_published');
        $this->addSql('ALTER TABLE distribution_contents ADD access ENUM(\'1\', \'2\', \'3\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:DistributionAccessType)\', CHANGE is_public is_published TINYINT(1) NOT NULL');
    }
}
