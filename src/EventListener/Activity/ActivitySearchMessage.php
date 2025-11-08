<?php

namespace App\EventListener\Activity;

use App\Entity\SearchMessage;
use App\Contracts\Activity\ActivitySearchMessageInterface;
use App\Contracts\EncryptDecrypt\EncryptDecryptInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Trait\Activity\GenericActivityEntityTrait;

/**
 * Classe gérant l'activité liée aux messages de recherche.
 * Utilise le trait GenericActivityEntityTrait pour factoriser la logique d'encryptage/décryptage.
 */
class ActivitySearchMessage implements ActivitySearchMessageInterface
{
    use GenericActivityEntityTrait;

    public function __construct(EncryptDecryptInterface $encryptDecrypt)
    {
        $this->encryptDecrypt = $encryptDecrypt;
        $this->entityClass = SearchMessage::class;
    }

    /**
     * Retourne la liste des champs à encrypter/décrypter pour SearchMessage.
     */
    protected static function getFields(): array
    {
        return ['description', 'message', 'fileName']; // Champs standards à encrypter
    }

    /**
     * Retourne la liste des champs spécifiques pour SearchMessage.
     */
    protected static function getSpecialFields(): array
    {
        return []; // Champ spécifique (fichier)
    }

    /**
     * Décrypte les informations sensibles d'un message de recherche.
     *
     * @param SearchMessage $searchMessage L'entité SearchMessage à décrypter.
     */
    public function decryptSearchMessage(SearchMessage $searchMessage): void
    {
        $this->decrypt($searchMessage);
    }

    /**
     * Encrypte les informations sensibles d'un message de recherche.
     *
     * @param SearchMessage $searchMessage L'entité SearchMessage à encrypter.
     */
    public function encryptSearchMessage(SearchMessage $searchMessage): void
    {
        $this->encrypt($searchMessage);
    }
}
