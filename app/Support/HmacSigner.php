<?php

namespace App\Support;

use App\Contracts\SignerContract;

class HmacSigner implements SignerContract
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
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
