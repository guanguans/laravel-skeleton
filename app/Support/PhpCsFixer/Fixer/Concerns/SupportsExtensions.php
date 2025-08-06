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

use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionInterface;

/**
 * @mixin \App\Support\PhpCsFixer\Fixer\AbstractConfigurableFixer
 */
trait SupportsExtensions
{
    public const string EXTENSIONS = 'extensions';

    public function supports(\SplFileInfo $file): bool
    {
        return str($file->getExtension())->is($this->configuration[self::EXTENSIONS], true)
            || str($file->getBasename())->lower()->endsWith($this->configuration[self::EXTENSIONS]);
    }

    protected function fixerOptionOfExtensions(): FixerOptionInterface
    {
        return (new FixerOptionBuilder(self::EXTENSIONS, 'The file extensions to format.'))
            ->setAllowedTypes(['array'])
            ->setDefault($this->defaultExtensions())
            ->getOption();
    }

    /**
     * @return list<string>
     */
    abstract protected function defaultExtensions(): array;
}
