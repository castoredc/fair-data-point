<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201112155936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE data_dictionary (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_D095DFFFDE12AB56 (created_by), INDEX IDX_D095DFFF16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_dictionary_dependency (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_7E2D937EFE54D947 (group_id), INDEX IDX_7E2D937EDE12AB56 (created_by), INDEX IDX_7E2D937E16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_dictionary_dependency_group (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', combinator VARCHAR(255) NOT NULL COMMENT \'(DC2Type:DependencyCombinatorType)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_dictionary_dependency_rule (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', variable CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', operator VARCHAR(255) NOT NULL COMMENT \'(DC2Type:DependencyOperatorType)\', value VARCHAR(255) NOT NULL, INDEX IDX_73C34DF8CC4D878D (variable), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_dictionary_group (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_dictionary_version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dependencies CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, `order` INT NOT NULL, is_repeated TINYINT(1) DEFAULT \'0\' NOT NULL, is_dependent TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_F8ECFBB74CABF5D (data_dictionary_version), UNIQUE INDEX UNIQ_F8ECFBBEA0F708D (dependencies), INDEX IDX_F8ECFBBDE12AB56 (created_by), INDEX IDX_F8ECFBB16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_dictionary_option_group (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_EAE76F60DE12AB56 (created_by), INDEX IDX_EAE76F6016FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_dictionary_option_option (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', option_group CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, value LONGTEXT NOT NULL, INDEX IDX_FCBD70FC542BF9AD (option_group), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_dictionary_variable (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', `group` CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', option_group CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', label VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, format VARCHAR(255) DEFAULT NULL, data_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:DataDictionaryDataType)\', `order` INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_DB31EFA26DC044C5 (`group`), INDEX IDX_DB31EFA2542BF9AD (option_group), INDEX IDX_DB31EFA2DE12AB56 (created_by), INDEX IDX_DB31EFA216FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_dictionary_version (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_dictionary CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', version VARCHAR(255) NOT NULL COMMENT \'(DC2Type:version)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_74CABF5DD095DFFF (data_dictionary), INDEX IDX_74CABF5DDE12AB56 (created_by), INDEX IDX_74CABF5D16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE data_dictionary ADD CONSTRAINT FK_D095DFFFDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary ADD CONSTRAINT FK_D095DFFF16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_dependency ADD CONSTRAINT FK_7E2D937EFE54D947 FOREIGN KEY (group_id) REFERENCES data_dictionary_dependency_group (id)');
        $this->addSql('ALTER TABLE data_dictionary_dependency ADD CONSTRAINT FK_7E2D937EDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_dependency ADD CONSTRAINT FK_7E2D937E16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_dependency_group ADD CONSTRAINT FK_4C41AB91BF396750 FOREIGN KEY (id) REFERENCES data_dictionary_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_dictionary_dependency_rule ADD CONSTRAINT FK_73C34DF8CC4D878D FOREIGN KEY (variable) REFERENCES data_dictionary_variable (id)');
        $this->addSql('ALTER TABLE data_dictionary_dependency_rule ADD CONSTRAINT FK_73C34DF8BF396750 FOREIGN KEY (id) REFERENCES data_dictionary_dependency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_dictionary_group ADD CONSTRAINT FK_F8ECFBB74CABF5D FOREIGN KEY (data_dictionary_version) REFERENCES data_dictionary_version (id)');
        $this->addSql('ALTER TABLE data_dictionary_group ADD CONSTRAINT FK_F8ECFBBEA0F708D FOREIGN KEY (dependencies) REFERENCES data_dictionary_dependency_group (id)');
        $this->addSql('ALTER TABLE data_dictionary_group ADD CONSTRAINT FK_F8ECFBBDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_group ADD CONSTRAINT FK_F8ECFBB16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_option_group ADD CONSTRAINT FK_EAE76F60DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_option_group ADD CONSTRAINT FK_EAE76F6016FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_option_option ADD CONSTRAINT FK_FCBD70FC542BF9AD FOREIGN KEY (option_group) REFERENCES data_dictionary_option_group (id)');
        $this->addSql('ALTER TABLE data_dictionary_variable ADD CONSTRAINT FK_DB31EFA26DC044C5 FOREIGN KEY (`group`) REFERENCES data_dictionary_group (id)');
        $this->addSql('ALTER TABLE data_dictionary_variable ADD CONSTRAINT FK_DB31EFA2542BF9AD FOREIGN KEY (option_group) REFERENCES data_dictionary_option_group (id)');
        $this->addSql('ALTER TABLE data_dictionary_variable ADD CONSTRAINT FK_DB31EFA2DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_variable ADD CONSTRAINT FK_DB31EFA216FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_version ADD CONSTRAINT FK_74CABF5DD095DFFF FOREIGN KEY (data_dictionary) REFERENCES data_dictionary (id)');
        $this->addSql('ALTER TABLE data_dictionary_version ADD CONSTRAINT FK_74CABF5DDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_dictionary_version ADD CONSTRAINT FK_74CABF5D16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('DROP TABLE distribution_csv_elements');
        $this->addSql('ALTER TABLE distribution_contents ADD is_cached TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE distribution_csv ADD data_dictionary CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD data_dictionary_version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', DROP include_all');
        $this->addSql('ALTER TABLE distribution_csv ADD CONSTRAINT FK_D815CB1AD095DFFF FOREIGN KEY (data_dictionary) REFERENCES data_dictionary (id)');
        $this->addSql('ALTER TABLE distribution_csv ADD CONSTRAINT FK_D815CB1A74CABF5D FOREIGN KEY (data_dictionary_version) REFERENCES data_dictionary_version (id)');
        $this->addSql('CREATE INDEX IDX_D815CB1AD095DFFF ON distribution_csv (data_dictionary)');
        $this->addSql('CREATE INDEX IDX_D815CB1A74CABF5D ON distribution_csv (data_dictionary_version)');
        $this->addSql('ALTER TABLE distribution_rdf DROP is_cached');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_dictionary_version DROP FOREIGN KEY FK_74CABF5DD095DFFF');
        $this->addSql('ALTER TABLE distribution_csv DROP FOREIGN KEY FK_D815CB1AD095DFFF');
        $this->addSql('ALTER TABLE data_dictionary_dependency_group DROP FOREIGN KEY FK_4C41AB91BF396750');
        $this->addSql('ALTER TABLE data_dictionary_dependency_rule DROP FOREIGN KEY FK_73C34DF8BF396750');
        $this->addSql('ALTER TABLE data_dictionary_dependency DROP FOREIGN KEY FK_7E2D937EFE54D947');
        $this->addSql('ALTER TABLE data_dictionary_group DROP FOREIGN KEY FK_F8ECFBBEA0F708D');
        $this->addSql('ALTER TABLE data_dictionary_variable DROP FOREIGN KEY FK_DB31EFA26DC044C5');
        $this->addSql('ALTER TABLE data_dictionary_option_option DROP FOREIGN KEY FK_FCBD70FC542BF9AD');
        $this->addSql('ALTER TABLE data_dictionary_variable DROP FOREIGN KEY FK_DB31EFA2542BF9AD');
        $this->addSql('ALTER TABLE data_dictionary_dependency_rule DROP FOREIGN KEY FK_73C34DF8CC4D878D');
        $this->addSql('ALTER TABLE data_dictionary_group DROP FOREIGN KEY FK_F8ECFBB74CABF5D');
        $this->addSql('ALTER TABLE distribution_csv DROP FOREIGN KEY FK_D815CB1A74CABF5D');
        $this->addSql('CREATE TABLE distribution_csv_elements (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', distribution CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, field_id LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, variable_name LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_BF23FC98A4483781 (distribution), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE distribution_csv_elements ADD CONSTRAINT FK_BF23FC98A4483781 FOREIGN KEY (distribution) REFERENCES distribution_csv (id)');
        $this->addSql('DROP TABLE data_dictionary');
        $this->addSql('DROP TABLE data_dictionary_dependency');
        $this->addSql('DROP TABLE data_dictionary_dependency_group');
        $this->addSql('DROP TABLE data_dictionary_dependency_rule');
        $this->addSql('DROP TABLE data_dictionary_group');
        $this->addSql('DROP TABLE data_dictionary_option_group');
        $this->addSql('DROP TABLE data_dictionary_option_option');
        $this->addSql('DROP TABLE data_dictionary_variable');
        $this->addSql('DROP TABLE data_dictionary_version');
        $this->addSql('ALTER TABLE distribution_contents DROP is_cached');
        $this->addSql('DROP INDEX IDX_D815CB1AD095DFFF ON distribution_csv');
        $this->addSql('DROP INDEX IDX_D815CB1A74CABF5D ON distribution_csv');
        $this->addSql('ALTER TABLE distribution_csv ADD include_all TINYINT(1) NOT NULL, DROP data_dictionary, DROP data_dictionary_version');
        $this->addSql('ALTER TABLE distribution_rdf ADD is_cached TINYINT(1) NOT NULL');
    }
}
