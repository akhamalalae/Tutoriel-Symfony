<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116230731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer_message DROP FOREIGN KEY FK_FEFDFF0E9B909F76');
        $this->addSql('DROP INDEX IDX_FEFDFF0E9B909F76 ON answer_message');
        $this->addSql('ALTER TABLE answer_message CHANGE to_answer_id message_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE answer_message ADD CONSTRAINT FK_FEFDFF0E537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_FEFDFF0E537A1329 ON answer_message (message_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer_message DROP FOREIGN KEY FK_FEFDFF0E537A1329');
        $this->addSql('DROP INDEX IDX_FEFDFF0E537A1329 ON answer_message');
        $this->addSql('ALTER TABLE answer_message CHANGE message_id to_answer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE answer_message ADD CONSTRAINT FK_FEFDFF0E9B909F76 FOREIGN KEY (to_answer_id) REFERENCES discussion_message_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_FEFDFF0E9B909F76 ON answer_message (to_answer_id)');
    }
}
