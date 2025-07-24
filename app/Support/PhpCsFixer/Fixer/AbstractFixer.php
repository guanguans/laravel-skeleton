<?php

/** @noinspection PhpInternalEntityUsedInspection */
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

use App\Support\PhpCsFixer\Fixer\Concerns\AlwaysCandidate;

/**
 * @see \App\Support\PhpCsFixer\Fixer
 * @see \App\Support\PhpCsFixer\Fixer\AbstractFixer
 * @see \PhpCsFixer\AbstractFixer
 * @see \PhpCsFixer\Fixer
 * @see \PhpCsFixerCustomFixers\Fixer
 * @see \PhpCsFixerCustomFixers\Fixer\AbstractFixer
 * @see \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer
 * @see \Symplify\CodingStandard\Fixer\Annotation
 */
abstract class AbstractFixer extends \PhpCsFixer\AbstractFixer
{
    use AlwaysCandidate;

    public static function name(): string
    {
        return (new static)->getName();
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     * @noinspection MissingParentCallInspection
     * @noinspection PhpAttributeCanBeAddedToOverriddenMemberInspection
     */
    #[\Override]
    public function getName(): string
    {
        return \sprintf('User/%s', $this->getSortName());
    }

    public function getSortName(): string
    {
        return parent::getName();
    }
}
