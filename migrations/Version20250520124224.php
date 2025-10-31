<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520124224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer_message (id INT AUTO_INCREMENT NOT NULL, discussion_message_user_id INT DEFAULT NULL, message_id INT DEFAULT NULL, creator_user_id INT DEFAULT NULL, date_creation DATETIME NOT NULL, INDEX IDX_FEFDFF0E616AB49D (discussion_message_user_id), INDEX IDX_FEFDFF0E537A1329 (message_id), INDEX IDX_FEFDFF0E29FC6AE1 (creator_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discussion (id INT AUTO_INCREMENT NOT NULL, person_invitation_sender_id INT DEFAULT NULL, person_invitation_recipient_id INT DEFAULT NULL, creator_user_id INT DEFAULT NULL, modifier_user_id INT DEFAULT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME NOT NULL, person_invitation_sender_number_unread_messages INT DEFAULT NULL, person_invitation_recipient_number_unread_messages INT DEFAULT NULL, INDEX IDX_C0B9F90F9CFCE548 (person_invitation_sender_id), INDEX IDX_C0B9F90F684D8FD (person_invitation_recipient_id), INDEX IDX_C0B9F90F29FC6AE1 (creator_user_id), INDEX IDX_C0B9F90F65787AC2 (modifier_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discussion_message_user (id INT AUTO_INCREMENT NOT NULL, message_id INT DEFAULT NULL, discussion_id INT DEFAULT NULL, creator_user_id INT DEFAULT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME NOT NULL, INDEX IDX_DC70602E537A1329 (message_id), INDEX IDX_DC70602E1ADED311 (discussion_id), INDEX IDX_DC70602E29FC6AE1 (creator_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_message (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, creator_user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME NOT NULL, mime_type VARCHAR(255) DEFAULT NULL, original_name VARCHAR(255) DEFAULT NULL, INDEX IDX_EDDB8944537A1329 (message_id), INDEX IDX_EDDB894429FC6AE1 (creator_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, creator_user_id INT DEFAULT NULL, date_creation DATETIME DEFAULT NULL, date_modification DATETIME DEFAULT NULL, message LONGTEXT DEFAULT NULL, INDEX IDX_B6BD307F29FC6AE1 (creator_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE search_discussion (id INT AUTO_INCREMENT NOT NULL, creator_user_id INT DEFAULT NULL, description LONGTEXT NOT NULL, name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, created_this_month TINYINT(1) NOT NULL, date_creation DATETIME NOT NULL, INDEX IDX_180FC9C529FC6AE1 (creator_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE search_message (id INT AUTO_INCREMENT NOT NULL, creator_user_id INT DEFAULT NULL, description LONGTEXT NOT NULL, message VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, created_this_month TINYINT(1) NOT NULL, date_creation DATETIME NOT NULL, INDEX IDX_3B6CDFE529FC6AE1 (creator_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, date_of_birth DATETIME DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, job VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) NOT NULL, brochure_filename VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, instagram VARCHAR(255) DEFAULT NULL, linked_in VARCHAR(255) DEFAULT NULL, skills LONGTEXT DEFAULT NULL, reset_token VARCHAR(255) DEFAULT NULL, reset_token_expires_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer_message ADD CONSTRAINT FK_FEFDFF0E616AB49D FOREIGN KEY (discussion_message_user_id) REFERENCES discussion_message_user (id)');
        $this->addSql('ALTER TABLE answer_message ADD CONSTRAINT FK_FEFDFF0E537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE answer_message ADD CONSTRAINT FK_FEFDFF0E29FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F9CFCE548 FOREIGN KEY (person_invitation_sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F684D8FD FOREIGN KEY (person_invitation_recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F29FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F65787AC2 FOREIGN KEY (modifier_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion_message_user ADD CONSTRAINT FK_DC70602E537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE discussion_message_user ADD CONSTRAINT FK_DC70602E1ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id)');
        $this->addSql('ALTER TABLE discussion_message_user ADD CONSTRAINT FK_DC70602E29FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file_message ADD CONSTRAINT FK_EDDB8944537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE file_message ADD CONSTRAINT FK_EDDB894429FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F29FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE search_discussion ADD CONSTRAINT FK_180FC9C529FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE search_message ADD CONSTRAINT FK_3B6CDFE529FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer_message DROP FOREIGN KEY FK_FEFDFF0E616AB49D');
        $this->addSql('ALTER TABLE answer_message DROP FOREIGN KEY FK_FEFDFF0E537A1329');
        $this->addSql('ALTER TABLE answer_message DROP FOREIGN KEY FK_FEFDFF0E29FC6AE1');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F9CFCE548');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F684D8FD');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F29FC6AE1');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F65787AC2');
        $this->addSql('ALTER TABLE discussion_message_user DROP FOREIGN KEY FK_DC70602E537A1329');
        $this->addSql('ALTER TABLE discussion_message_user DROP FOREIGN KEY FK_DC70602E1ADED311');
        $this->addSql('ALTER TABLE discussion_message_user DROP FOREIGN KEY FK_DC70602E29FC6AE1');
        $this->addSql('ALTER TABLE file_message DROP FOREIGN KEY FK_EDDB8944537A1329');
        $this->addSql('ALTER TABLE file_message DROP FOREIGN KEY FK_EDDB894429FC6AE1');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F29FC6AE1');
        $this->addSql('ALTER TABLE search_discussion DROP FOREIGN KEY FK_180FC9C529FC6AE1');
        $this->addSql('ALTER TABLE search_message DROP FOREIGN KEY FK_3B6CDFE529FC6AE1');
        $this->addSql('DROP TABLE answer_message');
        $this->addSql('DROP TABLE discussion');
        $this->addSql('DROP TABLE discussion_message_user');
        $this->addSql('DROP TABLE file_message');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE search_discussion');
        $this->addSql('DROP TABLE search_message');
        $this->addSql('DROP TABLE user');
    }
}
