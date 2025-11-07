<?php

namespace App\EventListener\Activity;

use App\Entity\SearchMessage;
use App\EventListener\Contracts\EncryptDecrypt\EncryptDecryptInterface;
use App\EventListener\Contracts\Activity\ActivitySearchMessageInterface;

class ActivitySearchMessage implements ActivitySearchMessageInterface
{
    public function __construct(private EncryptDecryptInterface $encryptDecrypt)
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
