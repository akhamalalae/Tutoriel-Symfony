<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116142739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer_message (id INT AUTO_INCREMENT NOT NULL, discussion_message_user_id INT DEFAULT NULL, INDEX IDX_FEFDFF0E616AB49D (discussion_message_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer_message ADD CONSTRAINT FK_FEFDFF0E616AB49D FOREIGN KEY (discussion_message_user_id) REFERENCES discussion_message_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer_message DROP FOREIGN KEY FK_FEFDFF0E616AB49D');
        $this->addSql('DROP TABLE answer_message');
    }
}
