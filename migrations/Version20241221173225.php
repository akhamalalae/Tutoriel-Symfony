<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221173225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file_message ADD creator_user_id INT DEFAULT NULL, ADD date_creation DATETIME NOT NULL, ADD date_modification DATETIME NOT NULL');
        $this->addSql('ALTER TABLE file_message ADD CONSTRAINT FK_EDDB894429FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EDDB894429FC6AE1 ON file_message (creator_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file_message DROP FOREIGN KEY FK_EDDB894429FC6AE1');
        $this->addSql('DROP INDEX IDX_EDDB894429FC6AE1 ON file_message');
        $this->addSql('ALTER TABLE file_message DROP creator_user_id, DROP date_creation, DROP date_modification');
    }
}
