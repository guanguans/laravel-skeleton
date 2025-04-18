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

namespace App\Console\Commands;

use App\Console\Commands\Concerns\Rescuer;
use Cerbero\CommandValidator\ValidatesInput;

abstract class Command extends \Illuminate\Console\Command
{
    use Rescuer;
    use ValidatesInput;
}
