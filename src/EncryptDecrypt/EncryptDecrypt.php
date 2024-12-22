<?php

namespace App\EncryptDecrypt;

class EncryptDecrypt
{
    public function loadKey() : ?string
    {
        return $GLOBALS['app']->getContainer()->getParameter('key_secrets');
    }

    public function encrypt(?string $val) : ?string
    {
        if ($val === null || $val === '') {
            return $val;
        }

        $parameters = $this->ParametersToEncryptDecrypt($val);

        $encrypt = openssl_encrypt(
            $parameters['data'], 
            $parameters['method'], 
            $parameters['key'], 
            $parameters['options'],
            $parameters['iv']
        );

        return $encrypt;
    }

    public function decrypt(?string $val) : ?string
    {
        if ($val === null || $val === '') {
            return $val;
        }

        $parameters = $this->ParametersToEncryptDecrypt($val);

        $decrypt = openssl_decrypt(
            $parameters['data'], 
            $parameters['method'], 
            $parameters['key'], 
            $parameters['options'],
            $parameters['iv']
        );

        return $decrypt;
    }

    public function ParametersToEncryptDecrypt(string $val) : ?array
    {
        $key = $this->loadKey();
        $data = $val;
        $method = "AES-256-CBC";
        $options = 0;
        $iv = '1234567891011121';

        return [
            'data' => $data,
            'method' => $method,
            'key' => $key,
            'options' => $options,
            'iv' => $iv,
        ];
    }
}
