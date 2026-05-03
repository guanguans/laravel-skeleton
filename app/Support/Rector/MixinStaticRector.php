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

use App\Support\Attribute\Mixin;
use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\AttributeReflection;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PHPStan\ScopeFetcher;
use Rector\Privatization\NodeManipulator\VisibilityManipulator;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

final class MixinStaticRector extends AbstractRector
{
    public function __construct(
        private readonly BetterNodeFinder $betterNodeFinder,
        private readonly VisibilityManipulator $visibilityManipulator
    ) {}

    #[\Override]
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @see \Rector\Php80\Rector\ClassMethod\SetStateToStaticRector
     * @see \Illuminate\Support\Traits\Macroable
     *
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @throws \Rector\Exception\ShouldNotHappenException
     */
    #[\Override]
    public function refactor(Node $node): ?Node
    {
        if (
            $node->isPrivate()
            || !array_find(
                ScopeFetcher::fetch($node)->getClassReflection()->getAttributes(),
                static fn (AttributeReflection $attributeReflection): bool => $attributeReflection->getName() === Mixin::class
            ) instanceof AttributeReflection
        ) {
            return null;
        }

        $this->isUsedThisVariable($node)
            ? $this->visibilityManipulator->makeNonStatic($node)
            : $this->visibilityManipulator->makeStatic($node);

        return $node;
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
                    namespace App\Support\Mixin;

                    use App\Support\Attribute\Mixin;
                    use Illuminate\Support\Str;
                    use Mtownsend\ReadTime\ReadTime;

                    #[Mixin(Str::class)]
                    final class StrMixin
                    {
                        public function readTime(): \Closure
                        {
                            /**
                             * @param list<string>|string $content
                             */
                            return static fn (
                                array|string $content,
                                bool $omitSeconds = true,
                                bool $abbreviated = false,
                                int $wordsPerMinute = 230
                            ): string => new ReadTime($content, $omitSeconds, $abbreviated, $wordsPerMinute)->get();
                        }
                    }
                    PHP,
                <<<'PHP'
                    namespace App\Support\Mixin;

                    use App\Support\Attribute\Mixin;
                    use Illuminate\Support\Str;
                    use Mtownsend\ReadTime\ReadTime;

                    #[Mixin(Str::class)]
                    final class StrMixin
                    {
                        public static function readTime(): \Closure
                        {
                            /**
                             * @param list<string>|string $content
                             */
                            return static fn (
                                array|string $content,
                                bool $omitSeconds = true,
                                bool $abbreviated = false,
                                int $wordsPerMinute = 230
                            ): string => new ReadTime($content, $omitSeconds, $abbreviated, $wordsPerMinute)->get();
                        }
                    }
                    PHP,
            ),
            new CodeSample(
                <<<'PHP'
                    namespace App\Support\Mixin;

                    use App\Support\Attribute\Mixin;
                    use Illuminate\Support\Str;
                    use Illuminate\Support\Stringable;

                    #[Mixin(Stringable::class)]
                    final class StringableMixin
                    {
                        public static function readTime(): \Closure
                        {
                            return fn (
                                bool $omitSeconds = true,
                                bool $abbreviated = false,
                                int $wordsPerMinute = 230
                            ): Stringable => new Stringable(
                                Str::readTime($this->value, $omitSeconds, $abbreviated, $wordsPerMinute)
                            );
                        }
                    }
                    PHP,
                <<<'PHP'
                    namespace App\Support\Mixin;

                    use App\Support\Attribute\Mixin;
                    use Illuminate\Support\Str;
                    use Illuminate\Support\Stringable;

                    #[Mixin(Stringable::class)]
                    final class StringableMixin
                    {
                        public function readTime(): \Closure
                        {
                            return fn (
                                bool $omitSeconds = true,
                                bool $abbreviated = false,
                                int $wordsPerMinute = 230
                            ): Stringable => new Stringable(
                                Str::readTime($this->value, $omitSeconds, $abbreviated, $wordsPerMinute)
                            );
                        }
                    }
                    PHP,
            ),
        ];
    }

    private function isUsedThisVariable(ClassMethod $node): bool
    {
        return (bool) $this->betterNodeFinder->findFirst(
            (array) $node->getStmts(),
            fn (Node $subNode): bool => $subNode instanceof Variable && $this->isName($subNode, 'this')
        );
    }
}
