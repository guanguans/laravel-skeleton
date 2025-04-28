<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

arch('will not use debugging functions')
    ->expect([
        // 'dd',
        // 'die',
        // 'dump',
        // 'echo',
        // 'env',
        // 'env_explode',
        'env_getcsv',
        // 'exit',
        // 'print',
        // 'print_r',
        // 'printf',
        // 'ray',
        // 'trap',
        // 'var_dump',
        // 'var_export',
        // 'vprintf',
    ])
    ->each->not->toBeUsed();
