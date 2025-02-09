<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support;

use App\Support\Contracts\SignerContract;

readonly class HmacSigner implements SignerContract
{
    public function __construct(
        #[\SensitiveParameter]
        private string $secret,
        private string $algo = 'sha256'
    ) {}

    public function sign(array $payload): string
    {
        return hash_hmac($this->algo, $this->hashingDataFor($payload), $this->secret);
    }

    public function validate(string $signature, array $payload): bool
    {
        return hash_equals($signature, $this->sign($payload));
    }

    private function hashingDataFor(array $payload): string
    {
        return urldecode(http_build_query($this->sort($payload)));
    }

    private function sort(array $payload): array
    {
        ksort($payload);

        foreach ($payload as &$item) {
            \is_array($item) and $item = $this->sort($item);
        }

        return $payload;
    }
}
