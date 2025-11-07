<?php
// src/EventListener/Activity/ActivitySearchDiscussion.php

namespace App\EventListener\Activity;

use App\Entity\SearchDiscussion;
use App\EventListener\Contracts\Activity\ActivitySearchDiscussionInterface;
use App\EventListener\Contracts\EncryptDecrypt\EncryptDecryptInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Activity\Trait\GenericActivityEntityTrait;

/**
 * Classe gérant l'activité liée aux discussions de recherche.
 * Utilise le trait GenericActivityEntityTrait pour factoriser la logique d'encryptage/décryptage.
 */
class ActivitySearchDiscussion implements ActivitySearchDiscussionInterface
{
    use GenericActivityEntityTrait;

    public function __construct(EncryptDecryptInterface $encryptDecrypt)
    {
        $this->encryptDecrypt = $encryptDecrypt;
        $this->entityClass = SearchDiscussion::class;
    }

    /**
     * Retourne la liste des champs à encrypter/décrypter pour SearchDiscussion.
     */
    protected static function getFields(): array
    {
        return ['description', 'name', 'firstName']; // Champs standards à encrypter
    }

    /**
     * Retourne la liste des champs spécifiques pour SearchDiscussion.
     */
    protected static function getSpecialFields(): array
    {
        return []; // Pas de champs spéciaux pour SearchDiscussion
    }

    /**
     * Décrypte les informations sensibles d'une discussion de recherche.
     *
     * @param SearchDiscussion $searchDiscussion L'entité SearchDiscussion à décrypter.
     */
    public function decryptSearchDiscussion(SearchDiscussion $searchDiscussion): void
    {
        $this->decrypt($searchDiscussion);
    }

    /**
     * Encrypte les informations sensibles d'une discussion de recherche.
     *
     * @param SearchDiscussion $searchDiscussion L'entité SearchDiscussion à encrypter.
     */
    public function encryptSearchDiscussion(SearchDiscussion $searchDiscussion): void
    {
        $this->encrypt($searchDiscussion);
    }
}
