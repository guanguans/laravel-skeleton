<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Monolog\Formatter;

use Illuminate\Support\Collection;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\LogRecord;

class EloquentLogHttpModelFormatter extends NormalizerFormatter
{
    #[\Override]
    public function format(LogRecord $record): array
    {
        return $this->sanitizeContext(parent::format($record)['context']);
    }

    private function sanitizeContext(array $context): array
    {
        return collect($context)
            ->only(['method', 'path', 'request_header', 'input', 'response_header', 'response', 'ip', 'duration'])
            ->map(fn (mixed $value): string => \is_string($value) ? $value : $this->strFor($value))
            ->pipe(fn (Collection $context): array => [
                'method' => substr($context['method'], 0, 10),
                'path' => substr($context['path'], 0, 128),
                'request_header' => $this->textFor($context['request_header']),
                'input' => $this->textFor($context['input']),
                'response_header' => $this->textFor($context['response_header']),
                'response' => $this->textFor($context['response']),
                'ip' => substr($context['ip'], 0, 16),
                'duration' => substr($context['duration'], 0, 10),
            ]);
    }

    private function strFor(mixed $value, int $options = 0, int $depth = 512): string
    {
        try {
            return (string) json_encode(
                $value,
                $options | JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                $depth
            );
        } catch (\JsonException $jsonException) {
            return $jsonException->getMessage();
        }
    }

    private function textFor(string $content): string
    {
        // MySQL text 类型最大 64KB (65535)
        return substr($content, 0, 60 * 1024);
    }
}
