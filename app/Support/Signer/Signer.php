<?php

namespace App\Support\Signer;

abstract class Signer
{
    abstract public function sign(array $payload): string;

    public function validate(string $signature, array $payload): bool
    {
        return hash_equals($signature, $this->sign($payload));
    }

    protected function sort(array $payload)
    {
        $payload = array_filter($payload);

        ksort($payload);

        return $payload;
    }

    protected function getPreEncryptedData(array $payload): string
    {
        $sortedPayload = $this->sort($payload);

        return http_build_query($sortedPayload);
    }
}
