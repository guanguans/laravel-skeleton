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

use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\FixerInterface;

/**
 * @implements \IteratorAggregate<FixerInterface>
 *
 * @see \PhpCsFixerCustomFixers\Fixers
 * @see \ErickSkrauch\PhpCsFixer\Fixers
 */
final class Fixers implements \IteratorAggregate
{
    /**
     * @return \Generator<FixerInterface>
     */
    public function getIterator(): \Traversable
    {
        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ((Finder::create())->in(__DIR__.'/Fixer')->name('*Fixer.php') as $file) {
            // -4 is set to cut ".php" extension
            /** @var class-string<FixerInterface> $class */
            $class = __NAMESPACE__.str_replace('/', '\\', mb_substr($file->getPathname(), mb_strlen(__DIR__), -4));

            if (!class_exists($class) || !is_subclass_of($class, FixerInterface::class)) {
                continue;
            }

            $rfl = new \ReflectionClass($class);

            if (!$rfl->isInstantiable()) {
                continue;
            }

            yield new $class;
        }
    }
}
