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

namespace App\Support\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @see https://github.com/pelican-dev/panel/blob/main/app/PHPStan/ForbiddenGlobalFunctionsRule.php
 */
class ForbiddenGlobalFunctionsRule implements Rule
{
    /** @var list<string> */
    public const FORBIDDEN_FUNCTIONS = ['app', 'resolve'];

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        /** @var FuncCall $node */
        if ($node->name instanceof Name) {
            $functionName = (string) $node->name;

            if (\in_array($functionName, self::FORBIDDEN_FUNCTIONS, true)) {
                return [
                    RuleErrorBuilder::message(\sprintf(
                        'Usage of global function "%s" is forbidden.',
                        $functionName,
                    ))->identifier('myCustomRules.forbiddenGlobalFunctions')->build(),
                ];
            }
        }

        return [];
    }
}
