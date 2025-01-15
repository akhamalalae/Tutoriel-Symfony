<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\EncryptDecrypt\EncryptDecryptFile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DecryptFileExtension extends AbstractExtension
{
    public function __construct(
        private EncryptDecryptFile $encryptDecryptFile,
        private ParameterBagInterface $params
    )
    {}

    public function getFilters()
    {
        return [
            new TwigFilter('decrypt', [$this, 'decryptFile']),
        ];
    }

    public function decryptFile(string $file, $subDirectory) : string
    {        
        $source = $this->params->get('directory') . $subDirectory . $file;
        
        return base64_encode($this->encryptDecryptFile->decryptFile($source));
    }
}