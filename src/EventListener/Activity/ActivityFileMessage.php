<?php

namespace App\EventListener\Activity;

use App\Entity\FileMessage;
use App\EncryptDecrypt\EncryptDecrypt;

class ActivityFileMessage
{
    public function __construct(private EncryptDecrypt $encryptDecrypt)
    {
    }

    public function decryptFileMessage(FileMessage $fileMessage): void
    {
        $decryptName = $this->encryptDecrypt->decrypt($fileMessage->getName());
        $decryptMimeType = $this->encryptDecrypt->decrypt($fileMessage->getMimeType());
        $decryptOriginalName = $this->encryptDecrypt->decrypt($fileMessage->getOriginalName());

        $fileMessage->setSensitiveDataOriginalName($decryptOriginalName);
        $fileMessage->setSensitiveDataName($decryptName);
        $fileMessage->setSensitiveDataMimeType($decryptMimeType);
    }

    public function encryptFileMessage(FileMessage $fileMessage): void
    {
        $encryptName = $this->encryptDecrypt->encrypt($fileMessage->getName());
        $encryptMimeType = $this->encryptDecrypt->encrypt($fileMessage->getMimeType());
        $encryptOriginalName = $this->encryptDecrypt->encrypt($fileMessage->getOriginalName());

        $fileMessage->setName($encryptName);
        $fileMessage->setMimeType($encryptMimeType);
        $fileMessage->setOriginalName($encryptOriginalName);
    }
}
