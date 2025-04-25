<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Enums;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\Enum\Concerns\Enumerates;
use Elao\Enum\ExtrasTrait;
use Elao\Enum\ReadableEnumTrait;

enum HealthCheckStatusEnum: string
{
    use Enumerates;
    // use ExtrasTrait;
    // use ReadableEnumTrait;

    #[Meta(description: 'OK')]
    case OK = '<info>ok</info>';

    #[Meta(description: 'WARNING')]
    case WARNING = '<comment>warning</comment>';

    #[Meta(description: 'FAILING')]
    case FAILING = '<error>failing</error>';
}
