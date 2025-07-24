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

namespace App\Support\PhpCsFixer\Fixer\InlineHtml;

use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;

/**
 * @see https://github.com/TheDragonCode/codestyler/blob/5.x/app/Fixers/JsonFixer.php
 * @see https://github.com/ergebnis/composer-normalize
 * @see https://github.com/Seldaek/jsonlint
 * @see \Symfony\Component\Serializer\Encoder\JsonEncoder
 */
final class JsonFixer extends AbstractInlineHtmlFixer
{
    public const string DECODE_ASSOCIATIVE = 'decode_associative';
    public const string DECODE_FLAGS = 'decode_flags';
    public const string ENCODE_FLAGS = 'encode_flags';

    #[\Override]
    protected function fixerOptions(): array
    {
        return [
            (new FixerOptionBuilder(self::DECODE_ASSOCIATIVE, 'Whether to decode JSON as an associative array.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(self::DECODE_FLAGS, 'The flags to use when decoding JSON.'))
                ->setAllowedTypes(['int'])
                ->setDefault(0)
                ->getOption(),
            (new FixerOptionBuilder(self::ENCODE_FLAGS, 'The flags to use when encoding JSON.'))
                ->setAllowedTypes(['int'])
                ->setDefault(\JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR)
                ->getOption(),
        ];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['json'];
    }

    /**
     * @throws \JsonException
     */
    #[\Override]
    protected function format(string $content): string
    {
        return json_encode(
            json_decode($content, $this->configuration[self::DECODE_ASSOCIATIVE], 512, \JSON_THROW_ON_ERROR | $this->configuration[self::DECODE_FLAGS]),
            \JSON_THROW_ON_ERROR | $this->configuration[self::ENCODE_FLAGS]
        );
    }
}
