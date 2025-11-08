<?php

namespace App\Trait\Activity;

use App\Contracts\EncryptDecrypt\EncryptDecryptInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use ReflectionException;

/**
 * Trait générique pour gérer l'encryptage/décryptage des entités.
 * À utiliser dans les classes ActivityUser, ActivityMessage, etc.
 */
trait GenericActivityEntityTrait
{
    /**
     * @var EncryptDecryptInterface Service d'encryptage/décryptage.
     */
    protected EncryptDecryptInterface $encryptDecrypt;

    /**
     * @var string Classe de l'entité gérée (ex: User::class).
     */
    protected string $entityClass;

    /**
     * Retourne la liste des champs à encrypter/décrypter.
     * Doit être implémentée par la classe utilisatrice.
     */
    abstract protected static function getFields(): array;

    /**
     * Retourne la liste des champs spécifiques (ex: fichiers).
     * Doit être implémentée par la classe utilisatrice.
     */
    abstract protected static function getSpecialFields(): array;

    /**
     * Encrypt les champs sensibles de l'entité avant sauvegarde.
     */
    protected function encrypt(object $entity): void
    {
        $this->processFields($entity, fn($value) => $this->encryptDecrypt->encrypt($value), 'set');
        $this->processSpecialFields($entity, fn($value) => $this->encryptDecrypt->encrypt($value));
    }

    /**
     * Décrypt les champs sensibles de l'entité après chargement.
     */
    protected function decrypt(object $entity): void
    {
        $this->processFields($entity, fn($value) => $this->encryptDecrypt->decrypt($value), 'setSensitiveData');
        $this->processSpecialFields($entity, fn($value) => $this->encryptDecrypt->decrypt($value));
    }

    /**
     * Encrypt les champs modifiés avant une mise à jour.
     */
    protected function encryptPreUpdate(object $entity, PreUpdateEventArgs $event): void
    {
        $allFields = array_merge(static::getFields(), static::getSpecialFields());
        foreach ($allFields as $field) {
            $this->encryptFieldIfChanged($event, $entity, $field);
        }
    }

    /**
     * Traite les champs standards (getter/setter classiques).
     */
    private function processFields(object $entity, callable $processor, string $setterPrefix): void
    {
        foreach (static::getFields() as $field) {
            try {
                $getter = 'get' . ucfirst($field);
                $setter = $setterPrefix . ucfirst($field);

                if (method_exists($entity, $getter) && method_exists($entity, $setter)) {
                    $value = $entity->$getter();
                    if ($value !== null) {
                        $entity->$setter($processor($value));
                    }
                }
            } catch (ReflectionException $e) {
                throw new \RuntimeException("Erreur lors du traitement du champ '$field': " . $e->getMessage());
            }
        }
    }

    /**
     * Traite les champs spécifiques (ex: fichiers).
     */
    private function processSpecialFields(object $entity, callable $processor): void
    {
        foreach (static::getSpecialFields() as $field) {
            $getter = 'get' . ucfirst($field);
            $setter = 'set' . ucfirst($field);

            if (method_exists($entity, $getter) && method_exists($entity, $setter)) {
                $value = $entity->$getter();
                if ($value !== null) {
                    $entity->$setter($processor($value));
                }
            }
        }
    }

    /**
     * Encrypt un champ si modifié lors d'un preUpdate.
     */
    private function encryptFieldIfChanged(PreUpdateEventArgs $event, object $entity, string $field): void
    {
        if ($event->hasChangedField($field)) {
            $newValue = $event->getNewValue($field);
            $setter = 'set' . ucfirst($field);
            if (method_exists($entity, $setter)) {
                $entity->$setter($this->encryptDecrypt->encrypt($newValue));
            }
        }
    }
}
