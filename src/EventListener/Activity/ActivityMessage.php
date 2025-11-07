<?php

namespace App\EventListener\Activity;

use App\Entity\Message;
use App\EventListener\Contracts\Activity\ActivityMessageInterface;
use App\EventListener\Contracts\EncryptDecrypt\EncryptDecryptInterface;

class ActivityMessage implements ActivityMessageInterface
{
    public function __construct(private EncryptDecryptInterface $encryptDecrypt)
    {
    }

    public function decryptMessage(Message $message): void
    {
        $decrypt = $this->encryptDecrypt->decrypt($message->getMessage());

        $message->setSensitiveDataMessage($decrypt);
    }

    public function encryptMessage(Message $message): void
    {
        $encrypt = $this->encryptDecrypt->encrypt($message->getMessage());

        $message->setMessage($encrypt);
    }
}
