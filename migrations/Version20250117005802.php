<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250117005802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer_message ADD creator_user_id INT DEFAULT NULL, ADD date_creation DATETIME NOT NULL');
        $this->addSql('ALTER TABLE answer_message ADD CONSTRAINT FK_FEFDFF0E29FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FEFDFF0E29FC6AE1 ON answer_message (creator_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer_message DROP FOREIGN KEY FK_FEFDFF0E29FC6AE1');
        $this->addSql('DROP INDEX IDX_FEFDFF0E29FC6AE1 ON answer_message');
        $this->addSql('ALTER TABLE answer_message DROP creator_user_id, DROP date_creation');
    }
}
