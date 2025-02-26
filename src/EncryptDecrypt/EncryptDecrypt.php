<?php

namespace App\EncryptDecrypt;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
class EncryptDecrypt
{
    private $params;

    const METHOD = 'AES-256-CBC';

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function loadKey() : ?string
    {
        return $this->params->get('key_secrets');
    }

    public function loadIV() : ?string
    {
        return $this->params->get('iv');
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
        $method = self::METHOD;
        $options = 0;
        $iv = $this->loadIV();

        return [
            'data' => $data,
            'method' => $method,
            'key' => $key,
            'options' => $options,
            'iv' => $iv,
        ];
    }
}
