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

namespace App\Support\PhpCsFixer;

use PhpCsFixer\Fixer\FixerInterface;

/**
 * @see \PhpCsFixerCustomFixers\Fixers
 * @see \ErickSkrauch\PhpCsFixer\Fixers
 */
final class Fixers implements \IteratorAggregate
{
    /**
     * @return \Generator<FixerInterface>
     */
    public function getIterator(): \Generator
    {
        $classNames = [];

        foreach (new \DirectoryIterator(__DIR__.'/Fixer') as $fileInfo) {
            $fileName = $fileInfo->getBasename('.php');

            if (\in_array(
                $fileName,
                [
                    '.',
                    '..',
                    'AbstractConfigurableFixer',
                    'AbstractFixer',
                    'AbstractInlineHtmlFixer',
                    'AbstractToolFixer',
                ],
                true
            )) {
                continue;
            }

            $classNames[] = __NAMESPACE__.'\\Fixer\\'.$fileName;
        }

        sort($classNames);

        foreach ($classNames as $className) {
            $fixer = new $className;

            // \assert($fixer instanceof FixerInterface);
            if (!$fixer instanceof FixerInterface) {
                continue;
            }

            yield $fixer;
        }
    }
}
