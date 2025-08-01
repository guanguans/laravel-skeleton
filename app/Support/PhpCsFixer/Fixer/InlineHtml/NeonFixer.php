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

use Nette\Neon\Neon;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;

/**
 * @see https://github.com/nette/neon
 */
final class NeonFixer extends AbstractInlineHtmlFixer
{
    public const string BLOCK_MODE = 'block_mode';
    public const string INDENTATION = 'indentation';

    #[\Override]
    protected function fixerOptions(): array
    {
        return [
            (new FixerOptionBuilder(self::BLOCK_MODE, 'Whether to use block mode for encoding.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(self::INDENTATION, 'The indentation string to use for encoding.'))
                ->setAllowedTypes(['string'])
                ->setDefault('    ')
                ->getOption(),
        ];
    }

    #[\Override]
    protected function defaultExtensions(): array
    {
        return ['neon', 'neon.dist'];
    }

    #[\Override]
    protected function format(string $content): string
    {
        return Neon::encode(
            Neon::decode($content),
            $this->configuration[self::BLOCK_MODE],
            $this->configuration[self::INDENTATION]
        );
    }
}
