<?php

namespace App\EncryptDecrypt;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
class EncryptDecryptFile
{
    const FILE_ENCRYPTION_BLOCKS = 10000;

    public function __construct(private ParameterBagInterface $params)
    {
    }

    public function loadKey() : ?string
    {
        return $this->params->get('key_secrets');
    }

    /**
     * @param  $source  Path of the unencrypted file
     * @param  $dest  Path of the encrypted file to created
    */
    public function encryptFile($source, $dest) : void
    {
        $parameters = $this->ParametersToEncryptDecrypt();

        $cipher = $parameters['cipher'];

        $key = $parameters['key'];

        $ivLenght = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLenght);

        $fpSource = fopen($source, 'rb');
        $fpDest = fopen($dest, 'w');

        fwrite($fpDest, $iv);

        while (! feof($fpSource)) {
            $plaintext = fread($fpSource, $ivLenght * self::FILE_ENCRYPTION_BLOCKS);
            $ciphertext = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $iv = substr($ciphertext, 0, $ivLenght);

            fwrite($fpDest, $ciphertext);
        }

        fclose($fpSource);
        fclose($fpDest);
        unlink($source);
    }

    /**
     * @param  $source  Path of the encrypted file
    */
    public function decryptFile($source) : string
    {
        $parameters = $this->ParametersToEncryptDecrypt();

        $cipher = $parameters['cipher'];

        $key = $parameters['key'];

        $ivLenght = openssl_cipher_iv_length($cipher);

        $fpSource = fopen($source, 'rb');

        $iv = fread($fpSource, $ivLenght);

        $text = '';

        while (! feof($fpSource)) {
            $ciphertext = fread($fpSource, $ivLenght * (self::FILE_ENCRYPTION_BLOCKS + 1));
            $plaintext = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $iv = substr($plaintext, 0, $ivLenght);

            $text = $text . $plaintext;
        }

        fclose($fpSource);

        return $text;
    }

    public function ParametersToEncryptDecrypt() : ?array
    {
        return [
            'cipher' => 'aes-256-cbc',
            'key' => $this->loadKey()
        ];
    }
}
