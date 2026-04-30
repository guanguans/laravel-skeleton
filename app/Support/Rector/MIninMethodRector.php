<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Rector;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

final class MIninMethodRector extends AbstractRector
{
    #[\Override]
    public function getNodeTypes(): array
    {
        return [Node\Stmt\ClassMethod::class];
    }

    /**
     * @todo
     *
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     */
    #[\Override]
    public function refactor(Node $node): ?Node
    {
        return null;
    }

    /**
     * @return list<\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample>
     */
    #[\Override]
    protected function codeSamples(): array
    {
        return [
            new CodeSample(
                <<<'PHP'

                    PHP,
                <<<'PHP'

                    PHP,
            ),
        ];
    }
}
