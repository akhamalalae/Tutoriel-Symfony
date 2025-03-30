<?php

namespace App\Services\Message;

use App\Entity\Message;
use App\Entity\FileMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\File\FileUploader;
use Symfony\Component\Form\FormInterface;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\File as FileConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesMessageUploaderService
{
    private const DIRECTORY_FILES_MESSAGE = 'files/message';
    private const MAX_FILE_SIZE = 5242880; // 5MB
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileUploader $fileUploader,
        private readonly ValidatorInterface $validator
    ) {}

    /**
     * Upload files associated with a message
     *
     * @param User $user The user uploading the files
     * @param FormInterface $messageForm The form containing the files
     * @throws \Exception If file upload fails
     */
    public function uploader(User $user, FormInterface $messageForm): void
    {
        $files = $messageForm->get('files')->getData();
        $message = $messageForm->getData();
        
        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            try {
                $this->validateFile($file);
                $fileUploader = $this->fileUploader->upload($file, self::DIRECTORY_FILES_MESSAGE);

                $fileMessage = new FileMessage();
                $fileMessage
                    ->setName($fileUploader['name'])
                    ->setMimeType($fileUploader['mimeType'])
                    ->setOriginalName($fileUploader['originalName'])
                    ->setMessage($message)
                    ->setCreatorUser($user)
                    ->setDateCreation(new \DateTime())
                    ->setDateModification(new \DateTime());

                $this->em->persist($fileMessage);
                $message->addFileMessage($fileMessage);
            } catch (\Exception $e) {
                throw new \Exception('Failed to upload file: ' . $e->getMessage());
            }
        }

        $this->em->persist($message);
        $this->em->flush();
    }

    /**
     * Validate a file before upload
     *
     * @param UploadedFile $file The file to validate
     * @throws \Exception If file validation fails
     */
    private function validateFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('File size exceeds maximum allowed size of 5MB');
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new \Exception('File type not allowed');
        }

        // Validate file using Symfony's validator
        $constraint = new FileConstraint([
            'maxSize' => self::MAX_FILE_SIZE,
            'mimeTypes' => self::ALLOWED_MIME_TYPES,
            'mimeTypesMessage' => 'Please upload a valid file type',
            'maxSizeMessage' => 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.',
        ]);

        $violations = $this->validator->validate($file, $constraint);
        if (count($violations) > 0) {
            throw new \Exception($violations[0]->getMessage());
        }
    }
}