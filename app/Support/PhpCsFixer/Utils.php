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

    public static function output(): SymfonyStyle
    {
        static $symfonyStyle;

        if ($symfonyStyle) {
            return $symfonyStyle;
        }

        $argvInput = new ArgvInput;
        $consoleOutput = new ConsoleOutput;

        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        (fn () => $this->configureIO($argvInput, $consoleOutput))->call(new Application);

        return $symfonyStyle = new SymfonyStyle($argvInput, $consoleOutput);
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

    public static function formatXmlAttributes(
        string $content,
        int $multilineAttrThreshold = 5,
        int $indent = 2
    ): string {
        return preg_replace_callback(
            '/<([^\s>\/]+)(\s+[^>]+?)(\s*\/?)>/',
            static function (array $matches) use ($multilineAttrThreshold, $content, $indent): string {
                [$fullTag, $tagName, $attrs, $selfClose] = $matches;

                // 属性数量小于阈值保持单行
                if (preg_match_all('/\s+[^\s=]+="/', $attrs) < $multilineAttrThreshold) {
                    return $fullTag;
                }

                // 计算当前行的缩进
                $currentPos = strpos($content, $fullTag);
                $lineStart = strrpos(substr($content, 0, $currentPos), \PHP_EOL);
                $currentIndent = '';

                if (false !== $lineStart) {
                    $lineText = substr($content, $lineStart + 1, $currentPos - $lineStart - 1);
                    $currentIndent = str_repeat(' ', \strlen($lineText) - \strlen(ltrim($lineText)));
                }

                // 格式化属性为多行
                $attrIndent = str_repeat(' ', $indent);
                $multilineAttrs = preg_replace('/\s+([^\s=]+="[^"]*")/', "\n$currentIndent$attrIndent$1", $attrs);
                $tagClose = $selfClose ? "\n$currentIndent$selfClose>" : "\n$currentIndent>";

                return "<$tagName$multilineAttrs$tagClose";
            },
            $content
        );
    }
}
