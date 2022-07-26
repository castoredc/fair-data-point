<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add new columns to the castor_servers table to store
 * OAuth2 Client ID and Client secret. Make the `id` AUTOINCREMENT.
 */
final class Version20220726121248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new columns to the castor_servers table to store OAuth2 Client ID and Client secret.';
    }

    public function up(Schema $schema): void
    {
        $migration = <<<'EOF'
        SET FOREIGN_KEY_CHECKS=0;
        ALTER TABLE `fdp`.`castor_server` 
            MODIFY COLUMN `id` int(11) NOT NULL AUTO_INCREMENT FIRST,
            ADD COLUMN `client_id` varchar(255) NULL AFTER `default`,
            ADD COLUMN `client_secret` varchar(255) NULL AFTER `client_id`;
        SET FOREIGN_KEY_CHECKS=1;
        EOF;
        $this->addSql($migration);
    }

    public function down(Schema $schema): void
    {
        $downMigration = <<<'EOF'
        SET FOREIGN_KEY_CHECKS=0;
        ALTER TABLE `fdp`.`castor_server` 
            MODIFY COLUMN `id` int(11) NOT NULL FIRST,
            DROP COLUMN `client_id`,
            DROP COLUMN `client_secret`;
        SET FOREIGN_KEY_CHECKS=1;
        EOF;
        $this->addSql($downMigration);
    }
}
