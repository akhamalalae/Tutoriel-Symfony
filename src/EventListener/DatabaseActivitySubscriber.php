<?php

namespace App\EventListener;

use App\Entity\Message;
use App\Entity\Discussion;
use App\Entity\DiscussionMessageUser;
use App\Entity\FileMessage;
use App\Entity\SearchMessage;
use App\Entity\User;
use App\Entity\SearchDiscussion;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use App\EventListener\Activity\ActivityFileMessage;
use App\EventListener\Activity\ActivityMessage;
use App\EventListener\Activity\ActivitySearchDiscussion;
use App\EventListener\Activity\ActivitySearchMessage;
use App\EventListener\Activity\ActivityUser;
use Doctrine\ORM\Event\PreUpdateEventArgs;

final class DatabaseActivitySubscriber implements EventSubscriberInterface
{   
    public function __construct(
        private ActivityFileMessage $activityFileMessage,
        private ActivityMessage $activityMessage,
        private ActivitySearchDiscussion $activitySearchDiscussion,
        private ActivitySearchMessage $activitySearchMessage,
        private ActivityUser $activityUser
    ){}
    
    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postPersist,
            Events::postRemove,
            Events::preUpdate,
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

    public function postLoad(LifecycleEventArgs $args)
    {
        $this->logActivity('postLoad', $args);
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $this->logActivityPreUpdate($event);
    }

    private function logActivity(string $action, LifecycleEventArgs $args): void
    {
        switch ($action) {
            case 'postPersist':
                $this->logActivityPostPersist($args);
            break;
            case 'prePersist':
                $this->logActivityPrePersist($args);
            break;
            case 'postLoad':
                $this->logActivityPostLoad($args);
            break;
        }
    }

    public function logActivityPreUpdate(PreUpdateEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof User) {
            $this->activityUser->encryptPreUpdateUser($entity, $event);
            return;
        }
    }

    private function logActivityPostPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Message) {
            $this->activityMessage->decryptMessage($entity);
            return;
        }

        if ($entity instanceof FileMessage) {
            $this->activityFileMessage->decryptFileMessage($entity);
            return;
        }

        if ($entity instanceof SearchMessage) {
            $this->activitySearchMessage->decryptSearchMessage($entity);
            return;
        }

        if ($entity instanceof SearchDiscussion) {
            $this->activitySearchDiscussion->decryptSearchDiscussion($entity);
            return;
        }

        if ($entity instanceof User) {
            $this->activityUser->decryptUser($entity);
            return;
        }
    }

    private function logActivityPrePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Message) {
            $this->activityMessage->encryptMessage($entity);
            return;
        }

        if ($entity instanceof FileMessage) {
            $this->activityFileMessage->encryptFileMessage($entity);
            return;
        }

        if ($entity instanceof SearchMessage) {
            $this->activitySearchMessage->encryptSearchMessage($entity);
            return;
        }

        if ($entity instanceof SearchDiscussion) {
            $this->activitySearchDiscussion->encryptSearchDiscussion($entity);
            return;
        }

        if ($entity instanceof User) {
            $this->activityUser->encryptUser($entity);
            return;
        }
    }

    private function logActivityPostLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Message) {
            $this->activityMessage->decryptMessage($entity);
            return;
        }

        if ($entity instanceof FileMessage) {
            $this->activityFileMessage->decryptFileMessage($entity);
            return;
        }

        if ($entity instanceof SearchMessage) {
            $this->activitySearchMessage->decryptSearchMessage($entity);
            return;
        }

        if ($entity instanceof SearchDiscussion) {
            $this->activitySearchDiscussion->decryptSearchDiscussion($entity);
            return;
        }

        if ($entity instanceof User) {
            $this->activityUser->decryptUser($entity);
            return;
        }
    }
}
