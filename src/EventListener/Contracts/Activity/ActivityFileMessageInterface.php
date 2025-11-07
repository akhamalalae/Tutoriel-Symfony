<?php
namespace App\EventListener\Contracts\Activity;

use App\Entity\FileMessage;

interface ActivityFileMessageInterface
{
    public function decryptFileMessage(FileMessage $fileMessage): void;
    public function encryptFileMessage(FileMessage $fileMessage): void;
}