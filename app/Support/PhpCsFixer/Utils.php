<?php

/** @noinspection PhpInternalEntityUsedInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\PhpCsFixer;

use App\Support\Console\SymfonyStyleFactory;
use PhpCsFixer\FileRemoval;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Utils
{
    private function __construct() {}

    public static function isDryRun(): bool
    {
        return \in_array('--dry-run', self::argv(), true);
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     */
    public static function argv(): array
    {
        return $_SERVER['argv'] ??= [];
    }

    public static function output(): SymfonyStyle
    {
        static $symfonyStyle;

        return $symfonyStyle ??= SymfonyStyleFactory::create();
    }

    /**
     * @see \Illuminate\Filesystem\Filesystem::delete()
     */
    public static function deferDelete(string ...$paths): void
    {
        foreach ($paths as $path) {
            ($fileRemoval ??= new FileRemoval)->observe($path);
        }
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     *
     * @see \PhpCsFixer\Utils::toString()
     */
    public static function toString(mixed $value): string
    {
        return \is_string($value)
            ? $value
            : json_encode(
                $value,
                \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT
            );
    }
}
