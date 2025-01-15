<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112010917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F4E0D5037');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FA216F35');
        $this->addSql('DROP INDEX IDX_C0B9F90FA216F35 ON discussion');
        $this->addSql('DROP INDEX IDX_C0B9F90F4E0D5037 ON discussion');
        $this->addSql('ALTER TABLE discussion ADD person_invitation_sender_id INT DEFAULT NULL, ADD person_invitation_recipient_id INT DEFAULT NULL, ADD person_invitation_sender_number_unread_messages INT DEFAULT NULL, ADD person_invitation_recipient_number_unread_messages INT DEFAULT NULL, DROP person_sender_id, DROP person_recipient_id, DROP person_sender_number_unread_messages, DROP person_recipient_number_unread_messages');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F9CFCE548 FOREIGN KEY (person_invitation_sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F684D8FD FOREIGN KEY (person_invitation_recipient_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90F9CFCE548 ON discussion (person_invitation_sender_id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90F684D8FD ON discussion (person_invitation_recipient_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F9CFCE548');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F684D8FD');
        $this->addSql('DROP INDEX IDX_C0B9F90F9CFCE548 ON discussion');
        $this->addSql('DROP INDEX IDX_C0B9F90F684D8FD ON discussion');
        $this->addSql('ALTER TABLE discussion ADD person_sender_id INT DEFAULT NULL, ADD person_recipient_id INT DEFAULT NULL, ADD person_sender_number_unread_messages INT DEFAULT NULL, ADD person_recipient_number_unread_messages INT DEFAULT NULL, DROP person_invitation_sender_id, DROP person_invitation_recipient_id, DROP person_invitation_sender_number_unread_messages, DROP person_invitation_recipient_number_unread_messages');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F4E0D5037 FOREIGN KEY (person_sender_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FA216F35 FOREIGN KEY (person_recipient_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C0B9F90FA216F35 ON discussion (person_recipient_id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90F4E0D5037 ON discussion (person_sender_id)');
    }
}
