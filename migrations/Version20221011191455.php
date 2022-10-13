<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221011191455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX userName ON users');
        $this->addSql('DROP INDEX userEmail ON users');
        $this->addSql('ALTER TABLE users CHANGE userName userName VARCHAR(255) NOT NULL, CHANGE userEmail userEmail VARCHAR(255) NOT NULL, CHANGE active active TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users CHANGE userName userName VARCHAR(155) NOT NULL, CHANGE userEmail userEmail VARCHAR(155) NOT NULL, CHANGE active active TINYINT(1) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX userName ON users (userName)');
        $this->addSql('CREATE UNIQUE INDEX userEmail ON users (userEmail)');
    }
}
