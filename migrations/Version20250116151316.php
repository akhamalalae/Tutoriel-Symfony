<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116151316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer_message ADD to_answer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE answer_message ADD CONSTRAINT FK_FEFDFF0E9B909F76 FOREIGN KEY (to_answer_id) REFERENCES discussion_message_user (id)');
        $this->addSql('CREATE INDEX IDX_FEFDFF0E9B909F76 ON answer_message (to_answer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer_message DROP FOREIGN KEY FK_FEFDFF0E9B909F76');
        $this->addSql('DROP INDEX IDX_FEFDFF0E9B909F76 ON answer_message');
        $this->addSql('ALTER TABLE answer_message DROP to_answer_id');
    }
}
