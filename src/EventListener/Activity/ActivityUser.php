<?php

namespace App\EventListener\Activity;

use App\Entity\User;
use App\EncryptDecrypt\EncryptDecrypt;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class ActivityUser
{
    public function __construct(private EncryptDecrypt $encryptDecrypt)
    {}

    public function decryptUser(User $user): void
    {
        $decryptName = $this->encryptDecrypt->decrypt($user->getName());
        $decryptFirstName  = $this->encryptDecrypt->decrypt($user->getFirstName());
        $decryptEmail = $this->encryptDecrypt->decrypt($user->getEmail());
        $decryptCompany = $this->encryptDecrypt->decrypt($user->getCompany());
        $decryptJob = $this->encryptDecrypt->decrypt($user->getJob());
        $decryptBrochureFilename = $this->encryptDecrypt->decrypt($user->getBrochureFilename());
        $decryptMimeType = $this->encryptDecrypt->decrypt($user->getMimeType());
        $decryptStreet = $this->encryptDecrypt->decrypt($user->getStreet());
        $decryptCity = $this->encryptDecrypt->decrypt($user->getCity());
        $decryptPostalCode = $this->encryptDecrypt->decrypt($user->getPostalCode());
        $decryptCountry = $this->encryptDecrypt->decrypt($user->getCountry());
        $decryptTwitter = $this->encryptDecrypt->decrypt($user->getTwitter());
        $decryptFacebook = $this->encryptDecrypt->decrypt($user->getFacebook());
        $decryptInstagram = $this->encryptDecrypt->decrypt($user->getInstagram());
        $decryptLinkedIn = $this->encryptDecrypt->decrypt($user->getLinkedIn());

        $user->setSensitiveDataName($decryptName)
            ->setSensitiveDataFirstName($decryptFirstName)
            ->setSensitiveDataEmail($decryptEmail)
            ->setSensitiveDataCompany($decryptCompany)
            ->setSensitiveDataJob($decryptJob)
            ->setSensitiveDataBrochureFilename($decryptBrochureFilename)
            ->setSensitiveDataMimeType($decryptMimeType)
            ->setSensitiveDataStreet($decryptStreet)
            ->setSensitiveDataCity($decryptCity)
            ->setSensitiveDataPostalCode($decryptPostalCode)
            ->setSensitiveDataCountry($decryptCountry)
            ->setSensitiveDataTwitter($decryptTwitter)
            ->setSensitiveDataFacebook($decryptFacebook)
            ->setSensitiveDataInstagram($decryptInstagram)
            ->setSensitiveDataLinkedIn($decryptLinkedIn);

        dump('decryptUser', $user);
    }

    public function encryptUser(User $user): void
    {
        $encryptName = $this->encryptDecrypt->encrypt($user->getName());
        $encryptFirstName  = $this->encryptDecrypt->encrypt($user->getFirstName());
        $encryptEmail = $this->encryptDecrypt->encrypt($user->getEmail());
        $encryptCompany = $this->encryptDecrypt->encrypt($user->getCompany());
        $encryptJob = $this->encryptDecrypt->encrypt($user->getJob());
        $encryptBrochureFilename = $this->encryptDecrypt->encrypt($user->getBrochureFilename());
        $encryptMimeType = $this->encryptDecrypt->encrypt($user->getMimeType());
        $encryptStreet = $this->encryptDecrypt->encrypt($user->getStreet());
        $encryptCity = $this->encryptDecrypt->encrypt($user->getCity());
        $encryptPostalCode = $this->encryptDecrypt->encrypt($user->getPostalCode());
        $encryptCountry = $this->encryptDecrypt->encrypt($user->getCountry());
        $encryptTwitter = $this->encryptDecrypt->encrypt($user->getTwitter());
        $encryptFacebook = $this->encryptDecrypt->encrypt($user->getFacebook());
        $encryptInstagram = $this->encryptDecrypt->encrypt($user->getInstagram());
        $encryptLinkedIn = $this->encryptDecrypt->encrypt($user->getLinkedIn());

        $user->setName($encryptName)
            ->setFirstName($encryptFirstName)
            ->setEmail($encryptEmail)
            ->setCompany($encryptCompany)
            ->setJob($encryptJob)
            ->setBrochureFilename($encryptBrochureFilename)
            ->setMimeType($encryptMimeType)
            ->setStreet($encryptStreet)
            ->setCity($encryptCity)
            ->setPostalCode($encryptPostalCode)
            ->setCountry($encryptCountry)
            ->setTwitter($encryptTwitter)
            ->setFacebook($encryptFacebook)
            ->setInstagram($encryptInstagram)
            ->setLinkedIn($encryptLinkedIn);
    }

    public function encryptPreUpdateUser(User $user, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('name')) {
            $oldValue = $event->getOldValue('name');
            $newValue = $event->getNewValue('name');

            $encryptName = $this->encryptDecrypt->encrypt($newValue);

            $user->setName($encryptName);
        }
        if ($event->hasChangedField('firstName')) {
            $newValue = $event->getNewValue('firstName');

            $encryptFirstName  = $this->encryptDecrypt->encrypt($newValue);

            $user->setFirstName($encryptFirstName);
        }

        if ($event->hasChangedField('company')) {
            $newValue = $event->getNewValue('company');

            $encryptCompany = $this->encryptDecrypt->encrypt($newValue);

            $user->setCompany($encryptCompany);
        }

        if ($event->hasChangedField('job')) {
            $newValue = $event->getNewValue('job');

            $encryptJob = $this->encryptDecrypt->encrypt($newValue);

            $user->setJob($encryptJob);
        }

        if ($event->hasChangedField('brochureFilename')) {
            $newValue = $event->getNewValue('brochureFilename');

            $encryptBrochureFilename = $this->encryptDecrypt->encrypt($newValue);

            $user->setBrochureFilename($encryptBrochureFilename);
        }

        if ($event->hasChangedField('mimeType')) {
            $newValue = $event->getNewValue('mimeType');

            $encryptMimeType = $this->encryptDecrypt->encrypt($newValue);

            $user->setMimeType($encryptMimeType);
        }

        if ($event->hasChangedField('street')) {
            $newValue = $event->getNewValue('street');

            $encryptStreet = $this->encryptDecrypt->encrypt($newValue);

            $user->setStreet($encryptStreet);
        }

        if ($event->hasChangedField('city')) {
            $newValue = $event->getNewValue('city');

            $encryptCity = $this->encryptDecrypt->encrypt($newValue);

            $user->setCity($encryptCity);
        }

        if ($event->hasChangedField('postal_code')) {
            $newValue = $event->getNewValue('postal_code');

            $encryptPostalCode = $this->encryptDecrypt->encrypt($newValue);

            $user->setPostalCode($encryptPostalCode);
        }

        if ($event->hasChangedField('country')) {
            $newValue = $event->getNewValue('country');

            $encryptCountry = $this->encryptDecrypt->encrypt($newValue);

            $user->setCountry($encryptCountry);
        }

        if ($event->hasChangedField('twitter')) {
            $newValue = $event->getNewValue('twitter');

            $encryptTwitter = $this->encryptDecrypt->encrypt($newValue);

            $user->setTwitter($encryptTwitter);
        }

        if ($event->hasChangedField('facebook')) {
            $newValue = $event->getNewValue('facebook');

            $encryptFacebook = $this->encryptDecrypt->encrypt($newValue);

            $user->setFacebook($encryptFacebook);
        }

        if ($event->hasChangedField('instagram')) {
            $newValue = $event->getNewValue('instagram');

            $encryptInstagram = $this->encryptDecrypt->encrypt($newValue);

            $user->setInstagram($encryptInstagram);
        }

        if ($event->hasChangedField('linkedIn')) {
            $newValue = $event->getNewValue('linkedIn');

            $encryptLinkedIn = $this->encryptDecrypt->encrypt($newValue);

            $user->setLinkedIn($encryptLinkedIn);
        }
    }
}
