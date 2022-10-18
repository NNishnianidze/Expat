<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221014152501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_resets DROP FOREIGN KEY FK_9EDAFEA17F449E57');
        $this->addSql('ALTER TABLE password_resets ADD id INT UNSIGNED AUTO_INCREMENT NOT NULL, DROP id_id, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_resets MODIFY id INT UNSIGNED NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON password_resets');
        $this->addSql('ALTER TABLE password_resets ADD id_id INT UNSIGNED NOT NULL, DROP id');
        $this->addSql('ALTER TABLE password_resets ADD CONSTRAINT FK_9EDAFEA17F449E57 FOREIGN KEY (id_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE password_resets ADD PRIMARY KEY (id_id)');
    }
}
