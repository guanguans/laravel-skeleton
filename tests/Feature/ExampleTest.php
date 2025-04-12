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

namespace Tests\Feature;

class ExampleTest extends TestCase
{
    public function testHttp(): void
    {
        $this->get('/')->assertOk();
    }

    public function testConsole(): void
    {
        $this->artisan('about')->assertOk();
    }
}
