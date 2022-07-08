<?php

namespace App\Support\Signer;

abstract class Signer
{
    abstract public function sign(array $payload): string;

    abstract public function validate(string $signature, array $payload): bool;

    protected function sort(array $payload): array
    {
        ksort($payload);

        foreach ($payload as &$item) {
            is_array($item) and $item = $this->sort($item);
        }

        return $payload;
    }

    protected function transformToPreEncryptedData(array $payload): string
    {
        $sortedPayload = $this->sort($payload);

        return http_build_query($sortedPayload);
    }
}
