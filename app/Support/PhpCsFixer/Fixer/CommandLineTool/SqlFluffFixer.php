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

use App\Support\PhpCsFixer\Fixer\CommandLineTool\Concerns\PostPathCommand;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;

/**
 * @see https://github.com/sqlfluff/sqlfluff
 */
final class SqlFluffFixer extends AbstractCommandLineToolFixer
{
    use PostPathCommand;
    public const string EXTENSIONS = 'extensions';

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    #[\Override]
    protected function fixerOptions(): array
    {
        return [
            (new FixerOptionBuilder(self::EXTENSIONS, 'The file extensions to format.'))
                ->setAllowedTypes(['array'])
                ->setDefault(['sql'])
                ->getOption(),
        ];
    }

    #[\Override]
    protected function defaultMainCommand(): array
    {
        return ['sqlfluff', 'format'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function defaultOptions(): array
    {
        return ['--dialect' => 'mysql'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return $this->configuration[self::EXTENSIONS];
    }
}
