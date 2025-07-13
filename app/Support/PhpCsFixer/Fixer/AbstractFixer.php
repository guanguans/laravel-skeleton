<?php

/** @noinspection MissingParentCallInspection */
/** @noinspection PhpInternalEntityUsedInspection */
/** @noinspection PhpMissingParentCallCommonInspection */
/** @noinspection SensitiveParameterInspection */

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

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @see \ErickSkrauch\PhpCsFixer\Fixer
 * @see \ErickSkrauch\PhpCsFixer\Fixer\AbstractFixer
 * @see \PhpCsFixer\AbstractFixer
 * @see \PhpCsFixer\Fixer
 * @see \PhpCsFixerCustomFixers\Fixer
 * @see \PhpCsFixerCustomFixers\Fixer\AbstractFixer
 * @see \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer
 * @see \Symplify\CodingStandard\Fixer\Annotation
 */
abstract class AbstractFixer extends \PhpCsFixer\AbstractFixer
{
    public static function name(): string
    {
        return (new static)->getName();
    }

    #[\Override]
    public function getName(): string
    {
        return \sprintf('User/%s', parent::getName());
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    #[\Override]
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }
}
