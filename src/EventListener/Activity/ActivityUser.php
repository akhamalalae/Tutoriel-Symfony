<?php

namespace App\EventListener\Activity;

use App\Entity\User;
use App\EventListener\Contracts\EncryptDecrypt\EncryptDecryptInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Contracts\Activity\ActivityUserInterface;

class ActivityUser implements ActivityUserInterface
{
    private const FIELDS = [
        'Name', 'FirstName', 'Email', 'Company', 'Job',
        'Street', 'City', 'PostalCode', 'Country',
        'Twitter', 'Facebook', 'Instagram', 'LinkedIn',
        'BrochureFilename', 'MimeType'
    ];

    private const FILE_FIELDS = ['BrochureFilename', 'MimeType'];

    public function __construct(private EncryptDecryptInterface $encryptDecrypt) {}

    public function decryptUser(User $user): void
    {
        $this->processFields($user, fn($value) => $this->encryptDecrypt->decrypt($value), 'setSensitiveData');
    }

    public function encryptUser(User $user): void
    {
        $this->processFields($user, fn($value) => $this->encryptDecrypt->encrypt($value), 'set');
        
        // Traitement spécifique pour les champs de fichier
        foreach (self::FILE_FIELDS as $field) {
            $getter = 'get' . $field;
            $setter = 'set' . $field;
            if (method_exists($user, $getter) && $user->$getter()) {
                $user->$setter($this->encryptDecrypt->encrypt($user->$getter()));
            }
        }
    }

    private function processFields(User $user, callable $processor, string $setterPrefix): void
    {
        foreach (self::FIELDS as $field) {
            $getter = 'get' . $field;
            $setter = $setterPrefix . $field;
            if (method_exists($user, $getter) && method_exists($user, $setter)) {
                $value = $user->$getter();
                $user->$setter($processor($value));
            }
        }
    }

    public function encryptPreUpdateUser(User $user, PreUpdateEventArgs $event): void
    {
        $fields = array_merge(self::FIELDS, self::FILE_FIELDS);
        foreach ($fields as $field) {
            $this->encryptFieldIfChanged($event, $user, lcfirst($field));
        }
    }

    private function encryptFieldIfChanged(PreUpdateEventArgs $event, User $user, string $field): void
    {
        if ($event->hasChangedField($field)) {
            $newValue = $event->getNewValue($field);
            $encrypted = $this->encryptDecrypt->encrypt($newValue);
            $setter = 'set' . ucfirst($field);
            $user->$setter($encrypted);
        }
    }
}
