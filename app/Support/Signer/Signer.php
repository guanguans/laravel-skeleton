<?php

namespace App\Support\Signer;

abstract class Signer
{
    abstract public function sign(array $payload): string;

    abstract public function validate(string $signature, array $payload): bool;

    protected function sort(array $payload): array
    {
        ksort($payload);

        return $payload;
    }

    protected function transformToPreEncryptedData(array $payload): string
    {
        $sortedPayload = $this->sort($payload);

        $preEncryptedData = '';
        foreach ($sortedPayload as $key => $val) {
            $preEncryptedData .= "&$key=$val";
        }

        return substr($preEncryptedData, 1);
    }
}
