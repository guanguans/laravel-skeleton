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

namespace App\Support\Rectors;

use Illuminate\Http\Request;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\Rector\AbstractRector;
use Symfony\Component\HttpFoundation\Response;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ClassHandleMethodRector extends AbstractRector
{
    public function __construct(
        private readonly DocBlockUpdater $docBlockUpdater,
        private readonly PhpDocInfoFactory $phpDocInfoFactory
    ) {}

    /**
     * @throws PoorDocumentationException
     * @throws ShouldNotHappenException
     */
    final public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add noinspections doc comment to declare',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
                        <?php

                        namespace App\Http\Middleware;

                        use Illuminate\Http\Request;

                        class VerifySignature
                        {
                            public function handle(Request $request, \Closure $next): mixed
                            {
                                return $next($request);
                            }
                        }
                        CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                        <?php

                        namespace App\Http\Middleware;

                        use Illuminate\Http\Request;
                        use Symfony\Component\HttpFoundation\Response;

                        class VerifySignature
                        {
                            public function handle(Request $request, \Closure $next): Response
                            {
                                return $next($request);
                            }
                        }
                        CODE_SAMPLE,
                    [],
                ),
            ],
        );
    }

    #[\Override]
    final public function getNodeTypes(): array
    {
        return [
            ClassMethod::class,
        ];
    }

    /**
     * @param Node\Stmt\ClassMethod $node
     */
    #[\Override]
    final public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node, 'handle')) {
            return null;
        }

        if (
            \count($node->params) >= 2
            && $node->params[0]->type
            && $node->params[1]->type
            && $this->getName($node->params[0]->type) === Request::class
            && $this->getName($node->params[1]->type) === 'Closure'
        ) {
            $this->updateDocBlock($node);

            if (null === $node->returnType || $this->getName($node->returnType) !== Response::class) {
                $node->returnType = new FullyQualified(Response::class);

                return $node;
            }
        }

        return null;
    }

    private function updateDocBlock(Node $node): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        $noinspectionTag = 'noinspection';
        $noinspectionTagValue = 'RedundantDocCommentTagInspection';

        if (
            collect($phpDocInfo->getTagsByName($noinspectionTag))
                ->filter(static fn (PhpDocTagNode $phpDocTagNode) => str($phpDocTagNode)->endsWith($noinspectionTagValue))
                ->isEmpty()
        ) {
            $this->addEmptyPhpDocTagNodeFor($phpDocInfo);
            $phpDocInfo->addPhpDocTagNode(new PhpDocTagNode(
                "@$noinspectionTag",
                new GenericTagValueNode($noinspectionTagValue)
            ));
        }

        if (
            collect($phpDocInfo->getParamTagValueNodes())
                ->filter(static fn (ParamTagValueNode $paramTagValueNode) => str($paramTagValueNode)->is(
                    '\Closure(\Illuminate\Http\Request):\Symfony\Component\HttpFoundation\Response $next'
                ))
                ->isEmpty()
        ) {
            // $this->addEmptyPhpDocTagNodeFor($phpDocInfo);
            // $phpDocInfo->addPhpDocTagNode(new PhpDocTagNode(
            //     '@param',
            //     new GenericTagValueNode('\Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response $next')
            // ));

            // \RectorPrefix202503\print_node($node);
        }

        $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);
    }

    private function addEmptyPhpDocTagNodeFor(PhpDocInfo $phpDocInfo): void
    {
        $phpDocInfo->addPhpDocTagNode(new PhpDocTagNode('', new GenericTagValueNode('')));
    }
}
