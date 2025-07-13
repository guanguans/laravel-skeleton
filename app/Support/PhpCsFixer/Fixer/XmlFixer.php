<?php

/** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\PhpCsFixer\Fixer;

use Illuminate\Support\Stringable;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @see https://github.com/TheDragonCode/codestyler/blob/5.x/app/Fixers/XmlFixer.php
 * @see https://github.com/schmittjoh/serializer
 * @see https://github.com/symfony/serializer
 * @see \Symfony\Component\Serializer\Encoder\XmlEncoder
 */
final class XmlFixer extends AbstractInlineHtmlFixer
{
    public const string CONTEXT = 'context';
    public const string MULTILINE_ATTRIBUTE_THRESHOLD = 'multiline_attribute_threshold';

    #[\Override]
    protected function fixerOptions(): array
    {
        return [
            (new FixerOptionBuilder(self::CONTEXT, 'The context options for the XML encoder.'))
                ->setAllowedTypes(['array'])
                ->setDefault([
                    self::MULTILINE_ATTRIBUTE_THRESHOLD => 0,
                    XmlEncoder::ENCODING => 'UTF-8',
                    XmlEncoder::FORMAT_OUTPUT => true,
                    XmlEncoder::LOAD_OPTIONS => \LIBXML_NONET | \LIBXML_NOBLANKS,
                    XmlEncoder::SAVE_OPTIONS => 0,
                ])
                ->getOption(),
        ];
    }

    #[\Override]
    protected function supportedExtensions(): array
    {
        return ['xml', 'xml.dist'];
    }

    /**
     * @see `php -l phpunit.xml`
     */
    #[\Override]
    protected function format(string $content): string
    {
        return str(
            $this->createXmlEncoder($content)->encode(
                $this->createXmlEncoder($content)->decode($content, 'xml'),
                // $this->createXmlDomDocument($content),
                'xml'
            )
        )
            ->when(
                0 === ($this->configuration[self::CONTEXT][XmlEncoder::SAVE_OPTIONS] & \LIBXML_NOEMPTYTAG),
                static fn (Stringable $content) => $content->replaceMatches('/<(\w+)([^>]*)>\s*<\/\1>/', '<$1$2/>')
            )
            ->pipe(fn (Stringable $content): string => $this->formatAttributesToMultiline($content->toString()))
            ->toString();
    }

    private function createXmlEncoder(string $content): XmlEncoder
    {
        static $xmlEncoder;

        return $xmlEncoder ??= new XmlEncoder(
            [
                XmlEncoder::ROOT_NODE_NAME => $this->createXmlDomDocument($content)->documentElement?->nodeName,
            ] + $this->configuration[self::CONTEXT]
        );
    }

    private function createXmlDomDocument(string $content): \DOMDocument
    {
        static $domDocument;

        if (null === $domDocument) {
            $domDocument = new \DOMDocument;
            $domDocument->loadXML($content);
        }

        return $domDocument;
    }

    private function formatAttributesToMultiline(string $content): string
    {
        return preg_replace_callback(
            '/<([^\s>\/]+)(\s+[^>]+?)(\s*\/?)>/',
            function (array $matches) use ($content): string {
                [$fullMatch, $tagName, $attributes, $selfClosing] = $matches;

                // 如果属性数量不超过阈值，保持单行
                if (
                    preg_match_all(
                        '/\s+[^\s=]+="/',
                        $attributes
                    ) <= $this->configuration[self::CONTEXT][self::MULTILINE_ATTRIBUTE_THRESHOLD]
                ) {
                    return $fullMatch;
                }

                // 计算当前行的缩进
                $position = strpos($content, $fullMatch);
                $lastNewline = strrpos(substr($content, 0, $position), \PHP_EOL);
                $currentIndent = '';

                if (false !== $lastNewline) {
                    $lineBeforeTag = substr($content, $lastNewline + 1, $position - $lastNewline - 1);
                    $currentIndent = str_repeat(' ', \strlen($lineBeforeTag) - \strlen(ltrim($lineBeforeTag)));
                }

                // 格式化属性为多行
                $formattedAttrs = preg_replace('/\s+([^\s=]+="[^"]*")/', "\n$currentIndent  $1", $attributes);
                $closing = $selfClosing ? "\n$currentIndent$selfClosing>" : "\n$currentIndent>";

                return "<$tagName$formattedAttrs$closing";
            },
            $content
        );
    }
}
