<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221013162303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE password_resets (id_id INT UNSIGNED NOT NULL, token VARCHAR(255) NOT NULL, userEmail_id INT UNSIGNED DEFAULT NULL, UNIQUE INDEX UNIQ_9EDAFEA15F37A13B (token), UNIQUE INDEX UNIQ_9EDAFEA17D015CE8 (userEmail_id), PRIMARY KEY(id_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE password_resets ADD CONSTRAINT FK_9EDAFEA17F449E57 FOREIGN KEY (id_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE password_resets ADD CONSTRAINT FK_9EDAFEA17D015CE8 FOREIGN KEY (userEmail_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_resets DROP FOREIGN KEY FK_9EDAFEA17F449E57');
        $this->addSql('ALTER TABLE password_resets DROP FOREIGN KEY FK_9EDAFEA17D015CE8');
        $this->addSql('DROP TABLE password_resets');
    }
}
