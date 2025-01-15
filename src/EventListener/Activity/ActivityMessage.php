<?php

namespace App\EventListener\Activity;

use App\Entity\Message;
use App\EncryptDecrypt\EncryptDecrypt;

class ActivityMessage
{
    public function __construct(private EncryptDecrypt $encryptDecrypt)
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
