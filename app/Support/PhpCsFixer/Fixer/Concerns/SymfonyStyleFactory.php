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

namespace App\Support\PhpCsFixer\Fixer\Concerns;

use Symfony\Component\Console\Style\SymfonyStyle;

trait SymfonyStyleFactory
{
    protected function makeOutput(): SymfonyStyle
    {
        static $symfonyStyle;

        return $symfonyStyle ??= \App\Support\Console\SymfonyStyleFactory::create();
    }
}
