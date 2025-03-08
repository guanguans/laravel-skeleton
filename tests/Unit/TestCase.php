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

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;

class TestCase extends \Tests\TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->alertUnwantedDBAccess();
    }

    private function alertUnwantedDBAccess(): void
    {
        DB::listen(static function ($query): void {
            throw new \RuntimeException("Database access detected: $query->sql");
        });
    }
}
