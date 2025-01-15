<?php

namespace App\EventListener\Activity;

use App\Entity\SearchMessage;
use App\EncryptDecrypt\EncryptDecrypt;

class ActivitySearchMessage
{
    public function __construct(private EncryptDecrypt $encryptDecrypt)
    {
    }

    public function decryptSearchMessage(SearchMessage $searchMessage): void
    {
        $decryptDescription = $this->encryptDecrypt->decrypt($searchMessage->getDescription());
        $decryptMessage = $this->encryptDecrypt->decrypt($searchMessage->getMessage());
        $decryptFileName = $this->encryptDecrypt->decrypt($searchMessage->getFileName());

        $searchMessage->setSensitiveDataDescription($decryptDescription);
        $searchMessage->setSensitiveDataMessage($decryptMessage);
        $searchMessage->setSensitiveDataFileName($decryptFileName);
    }

    public function encryptSearchMessage(SearchMessage $searchMessage): void
    {
        $encryptDescription = $this->encryptDecrypt->encrypt($searchMessage->getDescription());
        $encryptMessage = $this->encryptDecrypt->encrypt($searchMessage->getMessage());
        $encryptFileName = $this->encryptDecrypt->encrypt($searchMessage->getFileName());

        $searchMessage->setDescription($encryptDescription);
        $searchMessage->setMessage($encryptMessage);
        $searchMessage->setFileName($encryptFileName);
    }
}
