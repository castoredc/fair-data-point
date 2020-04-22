<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200422080703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM distribution_csv_elements');
        $this->addSql('DELETE FROM distribution_csv');
        $this->addSql('DELETE FROM distribution_rdf_modules');
        $this->addSql('DELETE FROM distribution_rdf');
        $this->addSql('DELETE FROM distribution');

        $this->addSql('CREATE TABLE distribution_contents (distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by VARCHAR(190) DEFAULT NULL, updated_by VARCHAR(190) DEFAULT NULL, access ENUM(\'1\', \'2\', \'3\') NOT NULL COMMENT \'(DC2Type:DistributionAccessType)\', is_published TINYINT(1) NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_91757299DE12AB56 (created_by), INDEX IDX_9175729916FE72E1 (updated_by), PRIMARY KEY(distribution)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE distribution_contents ADD CONSTRAINT FK_91757299A4483781 FOREIGN KEY (distribution) REFERENCES distribution (id)');
        $this->addSql('ALTER TABLE distribution_contents ADD CONSTRAINT FK_91757299DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE distribution_contents ADD CONSTRAINT FK_9175729916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE distribution_csv DROP FOREIGN KEY FK_D815CB1ABF396750');
        $this->addSql('ALTER TABLE distribution_csv CHANGE id distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_csv ADD CONSTRAINT FK_D815CB1AA4483781 FOREIGN KEY (distribution) REFERENCES distribution_contents (distribution) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_csv_elements DROP FOREIGN KEY FK_BF23FC986EB6DDB5');
        $this->addSql('DROP INDEX IDX_BF23FC986EB6DDB5 ON distribution_csv_elements');
        $this->addSql('ALTER TABLE distribution_csv_elements CHANGE distribution_id distribution CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_csv_elements ADD CONSTRAINT FK_BF23FC98A4483781 FOREIGN KEY (distribution) REFERENCES distribution_csv (distribution)');
        $this->addSql('CREATE INDEX IDX_BF23FC98A4483781 ON distribution_csv_elements (distribution)');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AFBF396750');
        $this->addSql('ALTER TABLE distribution_rdf CHANGE id distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AFA4483781 FOREIGN KEY (distribution) REFERENCES distribution_contents (distribution) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_rdf_modules DROP FOREIGN KEY FK_95CA20636EB6DDB5');
        $this->addSql('DROP INDEX IDX_95CA20636EB6DDB5 ON distribution_rdf_modules');
        $this->addSql('ALTER TABLE distribution_rdf_modules CHANGE distribution_id distribution CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf_modules ADD CONSTRAINT FK_95CA2063A4483781 FOREIGN KEY (distribution) REFERENCES distribution_rdf (distribution)');
        $this->addSql('CREATE INDEX IDX_95CA2063A4483781 ON distribution_rdf_modules (distribution)');
        $this->addSql('ALTER TABLE catalog CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE dataset CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE distribution DROP access, DROP dtype, DROP is_published, CHANGE created created DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE distribution_csv DROP FOREIGN KEY FK_D815CB1AA4483781');
        $this->addSql('ALTER TABLE distribution_rdf DROP FOREIGN KEY FK_DDC596AFA4483781');
        $this->addSql('DROP TABLE distribution_contents');
        $this->addSql('ALTER TABLE catalog CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE dataset CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE distribution ADD access ENUM(\'1\', \'2\', \'3\') CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:DistributionAccessType)\', ADD dtype VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, ADD is_published TINYINT(1) NOT NULL, CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE distribution_csv DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE distribution_csv CHANGE distribution id CHAR(36) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_csv ADD CONSTRAINT FK_D815CB1ABF396750 FOREIGN KEY (id) REFERENCES distribution (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_csv ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE distribution_csv_elements DROP FOREIGN KEY FK_BF23FC98A4483781');
        $this->addSql('DROP INDEX IDX_BF23FC98A4483781 ON distribution_csv_elements');
        $this->addSql('ALTER TABLE distribution_csv_elements CHANGE distribution distribution_id CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_csv_elements ADD CONSTRAINT FK_BF23FC986EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES distribution_csv (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_BF23FC986EB6DDB5 ON distribution_csv_elements (distribution_id)');
        $this->addSql('ALTER TABLE distribution_rdf DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE distribution_rdf CHANGE distribution id CHAR(36) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf ADD CONSTRAINT FK_DDC596AFBF396750 FOREIGN KEY (id) REFERENCES distribution (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribution_rdf ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE distribution_rdf_modules DROP FOREIGN KEY FK_95CA2063A4483781');
        $this->addSql('DROP INDEX IDX_95CA2063A4483781 ON distribution_rdf_modules');
        $this->addSql('ALTER TABLE distribution_rdf_modules CHANGE distribution distribution_id CHAR(36) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf_modules ADD CONSTRAINT FK_95CA20636EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES distribution_rdf (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_95CA20636EB6DDB5 ON distribution_rdf_modules (distribution_id)');
    }
}
