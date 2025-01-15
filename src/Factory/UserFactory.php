<?php

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use App\Services\File\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserFactory extends ModelFactory
{
    const URL_IMAGE = 'https://picsum.photos/200';
    
    public function __construct(
        private FileUploader $fileUploader,
        private ParameterBagInterface $params,
    )
    {}

    protected function getDefaults(): array
    {
        //$uploadDirectory = $this->params->get('directory').'img/avatars/';

        // Générer l'URL de l'image aléatoire
        $imageUrl = self::URL_IMAGE;

        // Download the image content
        $imageContent = file_get_contents($imageUrl);

        // Save the image temporarily
        $tempImagePath = sys_get_temp_dir() . '/' . uniqid('faker_image_') . '.jpg';

        file_put_contents($tempImagePath, $imageContent);

        // Create a Symfony File object
        $file = new UploadedFile(
            $tempImagePath,
            'faker_image.jpg',
            mime_content_type($tempImagePath),
            null,
            true
        );

        // Upload the file using FileUploader
        $fileInfo = $this->fileUploader->upload($file, 'img/avatars/');

        return [
            'name' => self::faker()->Name,
            'firstName' => self::faker()->FirstName,
            'email' => self::faker()->email,
            'password' => '$2y$13$DHxRe.l4Tcj/SAwqyxsWT.S7zDUGoi7tf29WU0.lRV3FCk608GzGC',
            'dateOfBirth' => self::faker()->dateTime(),
            'brochureFilename' => $fileInfo['name'],
            'mimeType' => $fileInfo['mimeType']
        ];
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}