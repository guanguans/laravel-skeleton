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

namespace App\Support\PhpCsFixer\Fixer\CommandLineTool;

/**
 * @see https://github.com/zhlint-project/zhlint
 */
final class ZhLintFixer extends AbstractCommandLineToolFixer
{
    #[\Override]
    protected function defaultCommand(): array
    {
        return ['zhlint'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return ['--fix'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['md', 'markdown', 'txt', 'text'];
    }

    protected function path(): string
    {
        return $this->relativePath(parent::path());
    }

    private function relativePath(string $path): string
    {
        $workingDir = $this->configuration[self::CWD] ?? getcwd();
        $pathParts = explode(\DIRECTORY_SEPARATOR, realpath($path) ?: $path);
        $workingDirParts = explode(\DIRECTORY_SEPARATOR, realpath($workingDir) ?: $workingDir);

        // 找到共同的根路径
        $commonLength = 0;
        $minLength = min(\count($pathParts), \count($workingDirParts));

        for ($index = 0; $index < $minLength; ++$index) {
            /** @noinspection OffsetOperationsInspection */
            if ($pathParts[$index] !== $workingDirParts[$index]) {
                break;
            }

            ++$commonLength;
        }

        // 计算需要返回的层级数
        $upLevels = \count($workingDirParts) - $commonLength;
        $downPath = \array_slice($pathParts, $commonLength);

        // 构建相对路径
        $relativeParts = array_merge(array_fill(0, $upLevels, '..'), $downPath);

        return implode(\DIRECTORY_SEPARATOR, $relativeParts);
    }
}
