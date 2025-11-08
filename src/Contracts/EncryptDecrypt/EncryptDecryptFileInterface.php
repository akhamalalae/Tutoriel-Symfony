<?php

namespace App\Contracts\EncryptDecrypt;

interface EncryptDecryptFileInterface
{
    public function encryptFile($source, $dest) : void;
    public function decryptFile($source) : string;
}