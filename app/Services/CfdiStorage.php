<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\Storage;

readonly class CfdiStorage
{
    public function __construct(private Encrypter $encrypter)
    {
    }

    public function putEncryptedXml(int $userId, string $rfc, string $uuid, string $xml): string
    {
        $path = $userId.'/'.$rfc.'/'.$uuid.'.xml.enc';
        Storage::disk('private')->put($path, $this->encrypter->encryptString($xml));

        return $path;
    }

    public function getDecryptedXml(string $path): string
    {
        return $this->encrypter->decryptString(Storage::disk('private')->get($path));
    }
}
