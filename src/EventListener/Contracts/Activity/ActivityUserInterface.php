<?php
namespace App\EventListener\Contracts\Activity;

use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;

interface ActivityUserInterface
{
    public function decryptUser(User $user): void;
    public function encryptUser(User $user): void;
    public function encryptPreUpdateUser(User $user, PreUpdateEventArgs $event): void;
}