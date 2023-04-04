<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230404125048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receipts CHANGE file_name filename VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE transactions ADD was_reviewed TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('DROP INDEX UNIQ_1483A5E9586CA949 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
        $this->addSql('ALTER TABLE users DROP userName');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD userName VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9586CA949 ON users (userName)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE transactions DROP was_reviewed');
        $this->addSql('ALTER TABLE receipts CHANGE filename file_name VARCHAR(255) NOT NULL');
    }
}
