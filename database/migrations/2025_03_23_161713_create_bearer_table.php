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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bearer_tokens', function (Blueprint $table): void {
            $table->id();
            $table->string('token')->unique();
            $table->string('description')->nullable();
            $table->text('domains')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->timestamps();
        });
    }
};
