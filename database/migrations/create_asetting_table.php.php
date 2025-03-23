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
        Schema::create('asettings', static function (Blueprint $table): void {
            $table->id();
            $table->string('group')->default('general')->index();
            $table->enum('type', ['string', 'integer', 'boolean', 'json', 'array', 'date'])->default('string')->index();
            $table->string('key')->nullable(false)->index();
            $table->jsonb('value')->nullable(false);
            $table->string('title')->nullable(false);
            $table->boolean('is_visible')->default(true);
            $table->unique(['group', 'key']);
            // Bu satır eklenmiştir.
            $table->timestamps();
        });
    }
};
