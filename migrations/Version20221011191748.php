<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221011191748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9586CA949 ON users (userName)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E91F76FA2 ON users (userEmail)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_1483A5E9586CA949 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E91F76FA2 ON users');
    }
}
