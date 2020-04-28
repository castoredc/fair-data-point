<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200428122305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rdf_triple (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', module CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', subject CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', predicate CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', object CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dtype VARCHAR(255) NOT NULL, INDEX IDX_264E40C4C242628 (module), UNIQUE INDEX UNIQ_264E40C4FBCE3E7A (subject), UNIQUE INDEX UNIQ_264E40C4301BAA7B (predicate), UNIQUE INDEX UNIQ_264E40C4A8ADABEC (object), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rdf_triple_element (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dtype VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rdf_triple_element_castor_entity (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', entity VARCHAR(190) DEFAULT NULL, INDEX IDX_2ACFB8EE284468 (entity), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rdf_triple_element_castor_value (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', field VARCHAR(190) DEFAULT NULL, value_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:CastorValueType)\', INDEX IDX_2FD386FF5BF54558 (field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rdf_triple_element_uri (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', uri VARCHAR(255) NOT NULL COMMENT \'(DC2Type:iri)\', label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rdf_triple ADD CONSTRAINT FK_264E40C4C242628 FOREIGN KEY (module) REFERENCES distribution_rdf_modules (id)');
        $this->addSql('ALTER TABLE rdf_triple ADD CONSTRAINT FK_264E40C4FBCE3E7A FOREIGN KEY (subject) REFERENCES rdf_triple_element (id)');
        $this->addSql('ALTER TABLE rdf_triple ADD CONSTRAINT FK_264E40C4301BAA7B FOREIGN KEY (predicate) REFERENCES rdf_triple_element (id)');
        $this->addSql('ALTER TABLE rdf_triple ADD CONSTRAINT FK_264E40C4A8ADABEC FOREIGN KEY (object) REFERENCES rdf_triple_element (id)');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_entity ADD CONSTRAINT FK_2ACFB8EE284468 FOREIGN KEY (entity) REFERENCES castor_entity (id)');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_entity ADD CONSTRAINT FK_2ACFB8EBF396750 FOREIGN KEY (id) REFERENCES rdf_triple_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_value ADD CONSTRAINT FK_2FD386FF5BF54558 FOREIGN KEY (field) REFERENCES castor_entity (id)');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_value ADD CONSTRAINT FK_2FD386FFBF396750 FOREIGN KEY (id) REFERENCES rdf_triple_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rdf_triple_element_uri ADD CONSTRAINT FK_791BA60DBF396750 FOREIGN KEY (id) REFERENCES rdf_triple_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE castor_entity ADD label VARCHAR(255) NOT NULL, ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE distribution_rdf DROP prefix');
        $this->addSql('ALTER TABLE distribution_rdf_modules DROP twig, CHANGE distribution distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rdf_triple DROP FOREIGN KEY FK_264E40C4FBCE3E7A');
        $this->addSql('ALTER TABLE rdf_triple DROP FOREIGN KEY FK_264E40C4301BAA7B');
        $this->addSql('ALTER TABLE rdf_triple DROP FOREIGN KEY FK_264E40C4A8ADABEC');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_entity DROP FOREIGN KEY FK_2ACFB8EBF396750');
        $this->addSql('ALTER TABLE rdf_triple_element_castor_value DROP FOREIGN KEY FK_2FD386FFBF396750');
        $this->addSql('ALTER TABLE rdf_triple_element_uri DROP FOREIGN KEY FK_791BA60DBF396750');
        $this->addSql('DROP TABLE rdf_triple');
        $this->addSql('DROP TABLE rdf_triple_element');
        $this->addSql('DROP TABLE rdf_triple_element_castor_entity');
        $this->addSql('DROP TABLE rdf_triple_element_castor_value');
        $this->addSql('DROP TABLE rdf_triple_element_uri');
        $this->addSql('ALTER TABLE castor_entity DROP label, DROP slug');
        $this->addSql('ALTER TABLE distribution_rdf ADD prefix LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE distribution_rdf_modules ADD twig LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE distribution distribution CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\'');
    }
}
