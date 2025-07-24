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

namespace App\Support\PhpCsFixer\Fixer\Tool;

use App\Support\PhpCsFixer\Fixer\Tool\Concerns\PostPathCommand;

/**
 * @see https://gitlab.gnome.org/GNOME/libxml2/-/wikis/home
 * @see https://gnome.pages.gitlab.gnome.org/libxml2/xmllint.html
 */
final class XmllintFixer extends AbstractToolFixer
{
    use PostPathCommand;

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function command(): array
    {
        return [...$this->postPathCommand(), $this->path()];
    }

    #[\Override]
    protected function defaultTool(): array
    {
        return ['xmllint'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function defaultArgs(): array
    {
        return [
            '--format',
            '--encode',
            'utf-8',
            '--pretty',
            1,
            '--output',
            // $this->path()
        ];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['xml', 'xml.dist'];
    }
}
