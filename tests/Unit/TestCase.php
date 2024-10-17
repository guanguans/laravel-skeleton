<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;

class TestCase extends \Tests\TestCase
{
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
