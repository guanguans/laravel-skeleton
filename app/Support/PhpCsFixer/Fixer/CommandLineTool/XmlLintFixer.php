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

use App\Support\PhpCsFixer\Utils;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;

/**
 * @see https://gitlab.gnome.org/GNOME/libxml2/-/wikis/home
 * @see https://gnome.pages.gitlab.gnome.org/libxml2/xmllint.html
 */
final class XmlLintFixer extends AbstractCommandLineToolFixer
{
    public const string MULTILINE_ATTR_THRESHOLD = 'multiline_attr_threshold';

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    #[\Override]
    protected function fixerOptions(): array
    {
        return [
            (new FixerOptionBuilder(self::MULTILINE_ATTR_THRESHOLD, 'The threshold for multiline attributes.'))
                ->setAllowedTypes(['int'])
                ->setDefault(5)
                ->getOption(),
        ];
    }

    #[\Override]
    protected function defaultCommand(): array
    {
        return ['xmllint'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return [
            // '--noblanks',
            // '--nocompact',
            '--format',
            '--pretty' => 1,
            '--output' => $this->path(),
            '--encode' => 'UTF-8',
        ];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function silentOptions(): array
    {
        return ['--quiet'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function debugOptions(): array
    {
        return ['--timing'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['xml', 'xml.dist'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function postFix(string $content): string
    {
        return Utils::formatXmlAttributes($content, $this->configuration[self::MULTILINE_ATTR_THRESHOLD]);
    }
}
