<?php

namespace App\EventListener\Activity;

use App\Entity\User;
use App\Contracts\Activity\ActivityUserInterface;
use App\Contracts\EncryptDecrypt\EncryptDecryptInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Trait\Activity\GenericActivityEntityTrait;

/**
 * Gestion des activités liées à l'entité User.
 */
class ActivityUser implements ActivityUserInterface
{
    use GenericActivityEntityTrait;

    public function __construct(EncryptDecryptInterface $encryptDecrypt)
    {
        $this->encryptDecrypt = $encryptDecrypt;
        $this->entityClass = User::class;
    }

    /**
     * {@inheritdoc}
     */
    protected static function getFields(): array
    {
        return [
            'name', 'firstName', 'email', 'company', 'job',
            'street', 'city', 'postalCode', 'country',
            'twitter', 'facebook', 'instagram', 'linkedIn',
            'brochureFilename', 'mimeType'
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected static function getSpecialFields(): array
    {
        return ['brochureFilename', 'mimeType'];
    }

    /**
     * Décrypte les informations d'un utilisateur.
     *
     * @param User $user L'entité User à décrypter.
     * 
     * @return void
     */
    public function decryptUser(User $user): void
    {
        $this->decrypt($user);
    }

    /**
     * Encrypte les informations d'un utilisateur.
     *
     * @param User $user L'entité User à encrypter.
     * 
     * @return void
     */
    public function encryptUser(User $user): void
    {
        $this->encrypt($user);
    }

    /**
     * Encrypte les informations d'un utilisateur avant la mise à jour.
     *
     * @param User $user L'entité User à encrypter.
     * @param PreUpdateEventArgs $event Les arguments de l'événement de pré-mise à jour.
     * 
     * @return void
     */
    public function encryptPreUpdateUser(User $user, PreUpdateEventArgs $event): void
    {
        $this->encryptPreUpdate($user, $event);
    }
}
