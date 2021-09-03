<?php

namespace App\Support\Signer;

class HmacSigner extends Signer
{
    private $secret;
    private $algo;

    public function __construct(string $secret = '', string $algo = 'sha256')
    {
        $this->secret = $secret;
        $this->algo = $algo;
    }

    public function sign(array $payload): string
    {
        $preEncryptedData = $this->getPreEncryptedData($payload);

        $hash = hash_hmac($this->algo, $preEncryptedData, $this->secret, true);

        return base64_encode($hash);
    }
}
