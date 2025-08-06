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

use PhpCsFixer\FileRemoval;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @method void configureIO(InputInterface $input, OutputInterface $output)
 */
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

    public static function createSymfonyStyle(?InputInterface $input = null, ?OutputInterface $output = null): SymfonyStyle
    {
        static $symfonyStyle;

        if ($symfonyStyle && !$input instanceof InputInterface && !$output instanceof OutputInterface) {
            return $symfonyStyle;
        }

        $input ??= new ArgvInput;
        $output ??= new ConsoleOutput;

        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        (fn () => $this->configureIO($input, $output))->call(new Application);

        return $symfonyStyle = new SymfonyStyle($input, $output);
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

    /**
     * @see \Illuminate\Filesystem\Filesystem::ensureDirectoryExists()
     * @see \Psl\Filesystem\create_temporary_file()
     * @see \Spatie\TemporaryDirectory\TemporaryDirectory
     */
    public static function createTemporaryFile(
        ?string $directory = null,
        ?string $prefix = null,
        ?string $extension = null,
        bool $deferDelete = true,
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

    /**
     * @see \Illuminate\Filesystem\Filesystem::delete()
     */
    public static function deferDelete(string ...$paths): void
    {
        foreach ($paths as $path) {
            ($fileRemoval ??= new FileRemoval)->observe($path);
        }
    }
}
