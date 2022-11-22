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
        return hash_hmac($this->algo, $this->transformToPreEncryptedData($payload), $this->secret);
    }

    public function validate(string $signature, array $payload): bool
    {
        return hash_equals($signature, $this->sign($payload));
    }
}
