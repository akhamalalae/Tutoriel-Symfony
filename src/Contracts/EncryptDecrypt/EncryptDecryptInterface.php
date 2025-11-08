<?php

namespace App\Contracts\EncryptDecrypt;

interface EncryptDecryptInterface
{
    public function encrypt(?string $val) : ?string;
    public function decrypt(?string $val) : ?string;
}