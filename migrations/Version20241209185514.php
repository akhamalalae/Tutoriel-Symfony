<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209185514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_169E6FB912469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discussion (id INT AUTO_INCREMENT NOT NULL, person_one_id INT DEFAULT NULL, person_two_id INT DEFAULT NULL, creator_user_id INT DEFAULT NULL, modifier_user_id INT DEFAULT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME NOT NULL, INDEX IDX_C0B9F90F673FFEA5 (person_one_id), INDEX IDX_C0B9F90FC63196A (person_two_id), INDEX IDX_C0B9F90F29FC6AE1 (creator_user_id), INDEX IDX_C0B9F90F65787AC2 (modifier_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discussion_message_user (id INT AUTO_INCREMENT NOT NULL, message_id INT DEFAULT NULL, discussion_id INT DEFAULT NULL, creator_user_id INT DEFAULT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME NOT NULL, INDEX IDX_DC70602E537A1329 (message_id), INDEX IDX_DC70602E1ADED311 (discussion_id), INDEX IDX_DC70602E29FC6AE1 (creator_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, creator_user_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, date_creation DATETIME DEFAULT NULL, date_modification DATETIME DEFAULT NULL, objet VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, INDEX IDX_B6BD307F29FC6AE1 (creator_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, name VARCHAR(255) DEFAULT NULL, date_of_birth DATETIME DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, job VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, brochure_filename VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F673FFEA5 FOREIGN KEY (person_one_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FC63196A FOREIGN KEY (person_two_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F29FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F65787AC2 FOREIGN KEY (modifier_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion_message_user ADD CONSTRAINT FK_DC70602E537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE discussion_message_user ADD CONSTRAINT FK_DC70602E1ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id)');
        $this->addSql('ALTER TABLE discussion_message_user ADD CONSTRAINT FK_DC70602E29FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F29FC6AE1 FOREIGN KEY (creator_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB912469DE2');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F673FFEA5');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FC63196A');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F29FC6AE1');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F65787AC2');
        $this->addSql('ALTER TABLE discussion_message_user DROP FOREIGN KEY FK_DC70602E537A1329');
        $this->addSql('ALTER TABLE discussion_message_user DROP FOREIGN KEY FK_DC70602E1ADED311');
        $this->addSql('ALTER TABLE discussion_message_user DROP FOREIGN KEY FK_DC70602E29FC6AE1');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F29FC6AE1');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE discussion');
        $this->addSql('DROP TABLE discussion_message_user');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE user');
    }
}
