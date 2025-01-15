<?php

namespace App\Services\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\EncryptDecrypt\EncryptDecryptFile;

class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
        private EncryptDecryptFile $encryptDecryptFile,
    ) {
    }

    public function upload(UploadedFile $file, string $subDirectory = ''): array
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->slug($originalFilename);

        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $mimeType = $file->getMimeType();

        try {
            $directory = $this->getTargetDirectory() . $subDirectory;

            $file->move($directory, $fileName);

            $source = $directory . '/' .$fileName;

            $dest = $directory . '/encrypt-' . $fileName;

            $this->encryptDecryptFile->encryptFile($source, $dest);
            
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        
        return [
            'name' => 'encrypt-' . $fileName,
            'originalName' => $file->getClientOriginalName(),
            'mimeType' => $mimeType,
        ];
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}