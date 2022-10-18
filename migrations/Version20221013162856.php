<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221013162856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_resets DROP FOREIGN KEY FK_9EDAFEA17D015CE8');
        $this->addSql('DROP INDEX UNIQ_9EDAFEA17D015CE8 ON password_resets');
        $this->addSql('ALTER TABLE password_resets ADD userEmail VARCHAR(255) NOT NULL, DROP userEmail_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_resets ADD userEmail_id INT UNSIGNED DEFAULT NULL, DROP userEmail');
        $this->addSql('ALTER TABLE password_resets ADD CONSTRAINT FK_9EDAFEA17D015CE8 FOREIGN KEY (userEmail_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9EDAFEA17D015CE8 ON password_resets (userEmail_id)');
    }
}
