<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200406084224 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE distribution_csv (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', include_all TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution_csv_elements (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', distribution_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', type VARCHAR(255) NOT NULL, field_id LONGTEXT DEFAULT NULL, variable_name LONGTEXT DEFAULT NULL, INDEX IDX_BF23FC986EB6DDB5 (distribution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE distribution_csv ADD CONSTRAINT FK_D815CB1ABF396750 FOREIGN KEY (id) REFERENCES distribution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_csv_elements ADD CONSTRAINT FK_BF23FC986EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES distribution_csv (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE distribution_csv_elements DROP FOREIGN KEY FK_BF23FC986EB6DDB5');
        $this->addSql('DROP TABLE distribution_csv');
        $this->addSql('DROP TABLE distribution_csv_elements');
    }
}
