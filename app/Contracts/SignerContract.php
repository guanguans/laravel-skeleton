<?php

namespace App\Contracts;

interface SignerContract
{
    public function sign(array $payload): string;

    public function validate(string $signature, array $payload): bool;
}
