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

    public static function createSingletonTemporaryFile(
        ?string $directory = null,
        ?string $prefix = null,
        ?string $extension = null,
        bool $deferDelete = true,
    ): string {
        static $temporaryFile;

        return $temporaryFile ??= self::createTemporaryFile($directory, $prefix, $extension, $deferDelete);
    }

    /**
     * @see \Psl\Filesystem\create_temporary_file()
     * @see \Spatie\TemporaryDirectory\TemporaryDirectory
     * @see \Illuminate\Filesystem\Filesystem::ensureDirectoryExists()
     */
    public static function createTemporaryFile(
        ?string $directory = null,
        ?string $prefix = null,
        ?string $extension = null,
        bool $deferDelete = true
    ): string {
        $directory ??= sys_get_temp_dir();

        if (!is_dir($directory) && !mkdir($directory, 0o755, true) && !is_dir($directory)) {
            throw new \RuntimeException("The directory [$directory] could not be created.");
        }

        $temporaryFile = tempnam($directory, $prefix ?? '');

        if (!$temporaryFile) {
            throw new \RuntimeException("Failed to create a temporary file in directory [$directory].");
        }

        if ($extension) {
            $isRenamed = rename($temporaryFile, $temporaryFile .= ".$extension");

            if (!$isRenamed) {
                throw new \RuntimeException("Failed to rename temporary file [$temporaryFile] with extension [$extension].");
            }
        }

        $deferDelete and self::deferDelete($temporaryFile);

        return $temporaryFile;
    }
}
