<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116142953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion_message_user DROP FOREIGN KEY FK_DC70602EAB0FA336');
        $this->addSql('DROP INDEX IDX_DC70602EAB0FA336 ON discussion_message_user');
        $this->addSql('ALTER TABLE discussion_message_user DROP answer_to_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion_message_user ADD answer_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE discussion_message_user ADD CONSTRAINT FK_DC70602EAB0FA336 FOREIGN KEY (answer_to_id) REFERENCES message (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_DC70602EAB0FA336 ON discussion_message_user (answer_to_id)');
    }
}
