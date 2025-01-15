<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250111235139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F673FFEA5');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FC63196A');
        $this->addSql('DROP INDEX IDX_C0B9F90FC63196A ON discussion');
        $this->addSql('DROP INDEX IDX_C0B9F90F673FFEA5 ON discussion');
        $this->addSql('ALTER TABLE discussion ADD person_sender_id INT DEFAULT NULL, ADD person_recipient_id INT DEFAULT NULL, ADD person_sender_number_unread_messages INT DEFAULT NULL, ADD person_recipient_number_unread_messages INT DEFAULT NULL, DROP person_one_id, DROP person_two_id, DROP person_one_number_unread_messages, DROP person_two_number_unread_messages');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F4E0D5037 FOREIGN KEY (person_sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FA216F35 FOREIGN KEY (person_recipient_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90F4E0D5037 ON discussion (person_sender_id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90FA216F35 ON discussion (person_recipient_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F4E0D5037');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FA216F35');
        $this->addSql('DROP INDEX IDX_C0B9F90F4E0D5037 ON discussion');
        $this->addSql('DROP INDEX IDX_C0B9F90FA216F35 ON discussion');
        $this->addSql('ALTER TABLE discussion ADD person_one_id INT DEFAULT NULL, ADD person_two_id INT DEFAULT NULL, ADD person_one_number_unread_messages INT DEFAULT NULL, ADD person_two_number_unread_messages INT DEFAULT NULL, DROP person_sender_id, DROP person_recipient_id, DROP person_sender_number_unread_messages, DROP person_recipient_number_unread_messages');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F673FFEA5 FOREIGN KEY (person_one_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FC63196A FOREIGN KEY (person_two_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C0B9F90FC63196A ON discussion (person_two_id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90F673FFEA5 ON discussion (person_one_id)');
    }
}
