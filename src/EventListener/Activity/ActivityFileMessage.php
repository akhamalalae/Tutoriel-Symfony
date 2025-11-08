<?php
// src/EventListener/Activity/ActivityFileMessage.php

namespace App\EventListener\Activity;

use App\Entity\FileMessage;
use App\Contracts\Activity\ActivityFileMessageInterface;
use App\Contracts\EncryptDecrypt\EncryptDecryptInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Trait\Activity\GenericActivityEntityTrait;

/**
 * Classe gérant l'activité liée aux fichiers de messages.
 * Utilise le trait GenericActivityEntityTrait pour factoriser la logique d'encryptage/décryptage.
 */
class ActivityFileMessage implements ActivityFileMessageInterface
{
    use GenericActivityEntityTrait;

    public function __construct(EncryptDecryptInterface $encryptDecrypt)
    {
        $this->encryptDecrypt = $encryptDecrypt;
        $this->entityClass = FileMessage::class;
    }

    /**
     * Retourne la liste des champs à encrypter/décrypter pour FileMessage.
     */
    protected static function getFields(): array
    {
        return ['name', 'mimeType', 'originalName']; // Pas de champs standards pour FileMessage (tous sont considérés comme spéciaux)
    }

    /**
     * Retourne la liste des champs spécifiques (fichiers) pour FileMessage.
     */
    protected static function getSpecialFields(): array
    {
        return []; // Champs spécifiques à FileMessage
    }

    /**
     * Décrypte les informations sensibles d'un FileMessage.
     *
     * @param FileMessage $fileMessage L'entité FileMessage à décrypter.
     */
    public function decryptFileMessage(FileMessage $fileMessage): void
    {
        $this->decrypt($fileMessage);
    }

    /**
     * Encrypte les informations sensibles d'un FileMessage.
     *
     * @param FileMessage $fileMessage L'entité FileMessage à encrypter.
     */
    public function encryptFileMessage(FileMessage $fileMessage): void
    {
        $this->encrypt($fileMessage);
    }
}