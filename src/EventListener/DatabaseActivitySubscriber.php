<?php

namespace App\EventListener;

use App\Entity\Message;
use App\Entity\Discussion;
use App\Entity\DiscussionMessageUser;
use App\Entity\FileMessage;
use App\Entity\SearchMessage;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use App\EncryptDecrypt\EncryptDecrypt;
final class DatabaseActivitySubscriber implements EventSubscriberInterface
{     
    public function __construct(private EncryptDecrypt $encryptDecrypt)
    {
    }
    
    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
            Events::postLoad
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->logActivity('postPersist', $args);
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->logActivity('prePersist', $args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->logActivity('remove', $args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->logActivity('postUpdate', $args);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $this->logActivity('postLoad', $args);
    }

    private function logActivity(string $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        switch ($action) {
            case 'postPersist':
                if ($entity instanceof Message) {
                    $this->loadAndPostPersistDecryptMessage($entity);
                    return;
                }
                if ($entity instanceof FileMessage) {
                    $this->loadAndPostPersistDecryptFileMessage($entity);
                    return;
                }
                if ($entity instanceof SearchMessage) {
                    $this->loadAndPostPersistDecryptSearchMessage($entity);
                    return;
                }
                break;
            case 'prePersist':
                if ($entity instanceof Message) {
                    $this->prePersistEncryptMessage($entity);
                    return;
                }
                if ($entity instanceof FileMessage) {
                    $this->prePersistEncryptFileMessage($entity);
                    return;
                }
                if ($entity instanceof SearchMessage) {
                    $this->prePersistEncryptSearchMessage($entity);
                    return;
                }
                break;
            case 'postLoad':
                if ($entity instanceof Message) {
                    $this->loadAndPostPersistDecryptMessage($entity);
                    return;
                }
                if ($entity instanceof FileMessage) {
                    $this->loadAndPostPersistDecryptFileMessage($entity);
                    return;
                }
                if ($entity instanceof SearchMessage) {
                    $this->loadAndPostPersistDecryptSearchMessage($entity);
                    return;
                }
                break;
        }
    }

    private function loadAndPostPersistDecryptSearchMessage(SearchMessage $searchMessage): void
    {
        $decryptDescription = $this->encryptDecrypt->decrypt($searchMessage->getDescription());
        $decryptMessage = $this->encryptDecrypt->decrypt($searchMessage->getMessage());
        $decryptFileName = $this->encryptDecrypt->decrypt($searchMessage->getFileName());

        $searchMessage->setSensitiveDataDescription($decryptDescription);
        $searchMessage->setSensitiveDataMessage($decryptMessage);
        $searchMessage->setSensitiveDataFileName($decryptFileName);
    }

    private function prePersistEncryptSearchMessage(SearchMessage $searchMessage): void
    {
        $encryptDescription = $this->encryptDecrypt->encrypt($searchMessage->getDescription());
        $encryptMessage = $this->encryptDecrypt->encrypt($searchMessage->getMessage());
        $encryptFileName = $this->encryptDecrypt->encrypt($searchMessage->getFileName());

        $searchMessage->setDescription($encryptDescription);
        $searchMessage->setMessage($encryptMessage);
        $searchMessage->setFileName($encryptFileName);
    }

    private function loadAndPostPersistDecryptFileMessage(FileMessage $fileMessage): void
    {
        $decryptName = $this->encryptDecrypt->decrypt($fileMessage->getName());
        $decryptMimeType = $this->encryptDecrypt->decrypt($fileMessage->getMimeType());
        $decryptOriginalName = $this->encryptDecrypt->decrypt($fileMessage->getOriginalName());

        $fileMessage->setSensitiveDataOriginalName($decryptOriginalName);
        $fileMessage->setSensitiveDataName($decryptName);
        $fileMessage->setSensitiveDataMimeType($decryptMimeType);
    }

    private function prePersistEncryptFileMessage(FileMessage $fileMessage): void
    {
        $encryptName = $this->encryptDecrypt->encrypt($fileMessage->getName());
        $encryptMimeType = $this->encryptDecrypt->encrypt($fileMessage->getMimeType());
        $encryptOriginalName = $this->encryptDecrypt->encrypt($fileMessage->getOriginalName());

        $fileMessage->setName($encryptName);
        $fileMessage->setMimeType($encryptMimeType);
        $fileMessage->setOriginalName($encryptOriginalName);
    }

    private function loadAndPostPersistDecryptMessage(Message $message): void
    {
        $decrypt = $this->encryptDecrypt->decrypt($message->getMessage());

        $message->setSensitiveDataMessage($decrypt);
    }

    private function prePersistEncryptMessage(Message $message): void
    {
        $encrypt = $this->encryptDecrypt->encrypt($message->getMessage());

        $message->setMessage($encrypt);
    }
}
