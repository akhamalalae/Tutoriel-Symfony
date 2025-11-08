<?php

namespace App\Contracts\Activity;

use App\Entity\Message;

interface ActivityMessageInterface
{
    public function decryptMessage(Message $message): void;
    public function encryptMessage(Message $message): void;
}