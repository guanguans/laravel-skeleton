<?php

declare(strict_types=1);

namespace App\Support\Http\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface Handler
{
    public function __invoke(RequestInterface $request, array $options): ResponseInterface;
}
