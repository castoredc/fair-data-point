<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200414091139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE catalog ADD created_by VARCHAR(190) DEFAULT NULL, ADD updated_by VARCHAR(190) DEFAULT NULL, ADD created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated DATETIME DEFAULT NULL, DROP issued, DROP modified');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C3247DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C324716FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1B2C3247DE12AB56 ON catalog (created_by)');
        $this->addSql('CREATE INDEX IDX_1B2C324716FE72E1 ON catalog (updated_by)');
        $this->addSql('ALTER TABLE dataset ADD created_by VARCHAR(190) DEFAULT NULL, ADD updated_by VARCHAR(190) DEFAULT NULL, ADD created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D0DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dataset ADD CONSTRAINT FK_B7A041D016FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B7A041D0DE12AB56 ON dataset (created_by)');
        $this->addSql('CREATE INDEX IDX_B7A041D016FE72E1 ON dataset (updated_by)');
        $this->addSql('ALTER TABLE distribution ADD created_by VARCHAR(190) DEFAULT NULL, ADD updated_by VARCHAR(190) DEFAULT NULL, ADD created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated DATETIME DEFAULT NULL, DROP issued, DROP modified');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A4483781DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE distribution ADD CONSTRAINT FK_A448378116FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A4483781DE12AB56 ON distribution (created_by)');
        $this->addSql('CREATE INDEX IDX_A448378116FE72E1 ON distribution (updated_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C3247DE12AB56');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C324716FE72E1');
        $this->addSql('DROP INDEX IDX_1B2C3247DE12AB56 ON catalog');
        $this->addSql('DROP INDEX IDX_1B2C324716FE72E1 ON catalog');
        $this->addSql('ALTER TABLE catalog ADD modified DATETIME NOT NULL, DROP created_by, DROP updated_by, DROP updated, CHANGE created issued DATETIME NOT NULL');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D0DE12AB56');
        $this->addSql('ALTER TABLE dataset DROP FOREIGN KEY FK_B7A041D016FE72E1');
        $this->addSql('DROP INDEX IDX_B7A041D0DE12AB56 ON dataset');
        $this->addSql('DROP INDEX IDX_B7A041D016FE72E1 ON dataset');
        $this->addSql('ALTER TABLE dataset DROP created_by, DROP updated_by, DROP created, DROP updated');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A4483781DE12AB56');
        $this->addSql('ALTER TABLE distribution DROP FOREIGN KEY FK_A448378116FE72E1');
        $this->addSql('DROP INDEX IDX_A4483781DE12AB56 ON distribution');
        $this->addSql('DROP INDEX IDX_A448378116FE72E1 ON distribution');
        $this->addSql('ALTER TABLE distribution ADD issued DATETIME NOT NULL, ADD modified DATETIME NOT NULL, DROP created_by, DROP updated_by, DROP created, DROP updated');
    }
}
