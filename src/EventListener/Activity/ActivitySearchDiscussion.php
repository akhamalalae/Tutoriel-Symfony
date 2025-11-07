<?php

namespace App\EventListener\Activity;

use App\Entity\SearchDiscussion;
use App\EventListener\Contracts\EncryptDecrypt\EncryptDecryptInterface;
use App\EventListener\Contracts\Activity\ActivitySearchDiscussionInterface;

class ActivitySearchDiscussion implements ActivitySearchDiscussionInterface
{
    public function __construct(private EncryptDecryptInterface $encryptDecrypt)
    {
    }

    public function decryptSearchDiscussion(SearchDiscussion $searchDiscussion): void
    {
        $decryptDescription = $this->encryptDecrypt->decrypt($searchDiscussion->getDescription());
        $decryptFirstName = $this->encryptDecrypt->decrypt($searchDiscussion->getFirstName());
        $decryptName = $this->encryptDecrypt->decrypt($searchDiscussion->getName());

        $searchDiscussion->setSensitiveDataDescription($decryptDescription);
        $searchDiscussion->setSensitiveDataName($decryptName);
        $searchDiscussion->setSensitiveDataFirstName($decryptFirstName);
    }

    public function encryptSearchDiscussion(SearchDiscussion $searchDiscussion): void
    {
        $encryptDescription = $this->encryptDecrypt->encrypt($searchDiscussion->getDescription());
        $encryptFirstName = $this->encryptDecrypt->encrypt($searchDiscussion->getFirstName());
        $encryptName = $this->encryptDecrypt->encrypt($searchDiscussion->getName());

        $searchDiscussion->setDescription($encryptDescription);
        $searchDiscussion->setFirstName($encryptFirstName);
        $searchDiscussion->setName($encryptName);
    }
}
