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

namespace App\Support\PhpCsFixer\Fixer\InlineHtml;

use App\Support\PhpCsFixer\Utils;
use Illuminate\Support\Stringable;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
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
    public const string MULTILINE_ATTR_THRESHOLD = 'multiline_attr_threshold';

    #[\Override]
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::CONTEXT, 'The context options for the XML encoder.'))
                ->setAllowedTypes(['array'])
                ->setDefault([
                    self::MULTILINE_ATTR_THRESHOLD => 5,
                    XmlEncoder::ENCODING => 'UTF-8',
                    XmlEncoder::FORMAT_OUTPUT => true,
                    // XmlEncoder::LOAD_OPTIONS => \LIBXML_NONET | \LIBXML_NOBLANKS,
                    XmlEncoder::SAVE_OPTIONS => 0,
                ])
                ->getOption(),
        ]);
    }

    #[\Override]
    protected function extensions(): array
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
                // $this->createXmlDomDocument($content),
                $this->createXmlEncoder($content)->decode($content, 'xml'),
                'xml'
            )
        )
            // 自闭合标签
            ->unless(
                $this->configuration[self::CONTEXT][XmlEncoder::SAVE_OPTIONS] & \LIBXML_NOEMPTYTAG,
                static fn (Stringable $content) => $content->replaceMatches('/<(\w+)([^>]*)>\s*<\/\1>/', '<$1$2/>')
            )
            ->when(
                $this->configuration[self::CONTEXT][XmlEncoder::FORMAT_OUTPUT],
                fn (Stringable $content): Stringable => str(
                    Utils::formatXmlAttributes(
                        $content->toString(),
                        $this->configuration[self::CONTEXT][self::MULTILINE_ATTR_THRESHOLD]
                    )
                )
            )
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
}
