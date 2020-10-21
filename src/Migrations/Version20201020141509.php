<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201020141509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affiliation (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', person CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', organization CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', department CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_EA72153034DCD176 (person), INDEX IDX_EA721530C1EE637C (organization), INDEX IDX_EA721530CD1DE18A (department), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE affiliation ADD CONSTRAINT FK_EA72153034DCD176 FOREIGN KEY (person) REFERENCES person (id)');
        $this->addSql('ALTER TABLE affiliation ADD CONSTRAINT FK_EA721530C1EE637C FOREIGN KEY (organization) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE affiliation ADD CONSTRAINT FK_EA721530CD1DE18A FOREIGN KEY (department) REFERENCES department (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE affiliation');
    }
}
