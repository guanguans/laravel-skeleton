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

use App\Support\PhpCsFixer\Fixer\CommandLineTool\Concerns\ReverseSuccessfulProcess;
use App\Support\PhpCsFixer\Fixer\Concerns\AlwaysCandidate;
use App\Support\PhpCsFixer\Fixer\Concerns\LowestPriority;
use App\Support\PhpCsFixer\Fixer\Concerns\SupportsExtensionsAndPathArg;
use function Illuminate\Support\php_binary;

/**
 * @see https://github.com/prettier/plugin-php/blob/main/docs/recipes/php-cs-fixer/PrettierPHPFixer.php
 * @see https://github.com/laravel/pint
 * @see https://github.com/super-linter/super-linter
 */
final class PintFixer extends AbstractCommandLineToolFixer
{
    use AlwaysCandidate;
    use LowestPriority;
    use ReverseSuccessfulProcess;
    use SupportsExtensionsAndPathArg;

    #[\Override]
    protected function defaultCommand(): array
    {
        return [php_binary(), 'vendor/bin/pint'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return [
            // '--ansi',
            // '--config=pint.json',
            // '--format=json',
            // '--output-format=txt',
            // '--output-to-file=.build/pint/.pint.output',
            // '--parallel',
            '--repair',
            '--silent',
            // '--test',
            // '-vv',
        ];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function defaultEnv(): array
    {
        return ['XDEBUG_MODE' => 'off'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['php'];
    }
}
