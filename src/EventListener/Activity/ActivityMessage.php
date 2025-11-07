<?php
// src/EventListener/Activity/ActivityMessage.php

namespace App\EventListener\Activity;

use App\Entity\Message;
use App\EventListener\Contracts\Activity\ActivityMessageInterface;
use App\EventListener\Contracts\EncryptDecrypt\EncryptDecryptInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Activity\Trait\GenericActivityEntityTrait;

/**
 * Classe gérant l'activité liée aux messages.
 * Utilise le trait GenericActivityEntityTrait pour factoriser la logique d'encryptage/décryptage.
 */
class ActivityMessage implements ActivityMessageInterface
{
    use GenericActivityEntityTrait;

    public function __construct(EncryptDecryptInterface $encryptDecrypt)
    {
        $this->encryptDecrypt = $encryptDecrypt;
        $this->entityClass = Message::class;
    }

    /**
     * {@inheritdoc}
     */
    protected static function getFields(): array
    {
        return ['message']; // Champ à encrypter/décrypter pour Message
    }

    /**
     * {@inheritdoc}
     */
    protected static function getSpecialFields(): array
    {
        return []; // Pas de champs spéciaux pour Message
    }

    /**
     * Décrypte le contenu d'un message.
     *
     * @param Message $message L'entité Message à décrypter.
     */
    public function decryptMessage(Message $message): void
    {
        $this->decrypt($message);
    }

    /**
     * Encrypte le contenu d'un message.
     *
     * @param Message $message L'entité Message à encrypter.
     */
    public function encryptMessage(Message $message): void
    {
        $this->encrypt($message);
    }
}
