<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Signers;

use App\Support\Contracts\SignerContract;

/**
 * @see https://github.com/mcordingley/LaravelSapient
 * @see https://github.com/paragonie/sapient
 * @see https://github.com/paragonie/sapient-js
 */
final readonly class HmacSigner implements SignerContract
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
