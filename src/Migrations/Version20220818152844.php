<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220818152844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a log table for executed SPARQL queries.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
CREATE TABLE log_sparql_query (
    id INT AUTO_INCREMENT NOT NULL,
    distribution_id TINYTEXT NOT NULL,
    user_id TINYTEXT NOT NULL,
    user_email TINYTEXT NOT NULL,
    queried_on DATETIME NOT NULL,
    sparql_query LONGTEXT NOT NULL,
    result_count INT NOT NULL,
    error LONGTEXT DEFAULT NULL,
PRIMARY KEY ( id )) DEFAULT CHARACTER 
SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = INNODB
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE log_sparql_query');
    }
}
