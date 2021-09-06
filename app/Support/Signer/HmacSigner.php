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

        $hash = hash_hmac($this->algo, $preEncryptedData, $this->secret);

        return md5($hash);
    }

    public function validate(string $signature, array $payload): bool
    {
        return hash_equals($signature, $this->sign($payload));
    }
}
