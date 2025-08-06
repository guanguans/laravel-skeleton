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
    protected function defaultExtensions(): array
    {
        return ['xml', 'xml.dist'];
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
            '--output' => $this->finalFile,
            '--encode' => 'UTF-8',
        ];
    }

    protected function fixedCode(): string
    {
        return $this->formatXmlAttributes(parent::fixedCode(), $this->configuration[self::MULTILINE_ATTR_THRESHOLD]);
    }

    private function formatXmlAttributes(string $xml, int $multilineAttrThreshold = 5, int $indent = 2): string
    {
        return preg_replace_callback(
            '/<([^\s>\/]+)(\s+[^>]+?)(\s*\/?)>/',
            static function (array $matches) use ($multilineAttrThreshold, $xml, $indent): string {
                [$fullTag, $tagName, $attrs, $selfClose] = $matches;

                // 属性数量小于阈值保持单行
                if (preg_match_all('/\s+[^\s=]+="/', $attrs) < $multilineAttrThreshold) {
                    return $fullTag;
                }

                // 计算当前行的缩进
                $currentPos = strpos($xml, $fullTag);
                $lineStart = strrpos(substr($xml, 0, $currentPos), \PHP_EOL);
                $currentIndent = '';

                if (false !== $lineStart) {
                    $lineText = substr($xml, $lineStart + 1, $currentPos - $lineStart - 1);
                    $currentIndent = str_repeat(' ', \strlen($lineText) - \strlen(ltrim($lineText)));
                }

                // 格式化属性为多行
                $attrIndent = str_repeat(' ', $indent);
                $multilineAttrs = preg_replace('/\s+([^\s=]+="[^"]*")/', "\n$currentIndent$attrIndent$1", $attrs);
                $tagClose = $selfClose ? "\n$currentIndent$selfClose>" : "\n$currentIndent>";

                return "<$tagName$multilineAttrs$tagClose";
            },
            $xml
        );
    }
}
