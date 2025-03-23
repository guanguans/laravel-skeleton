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
        // avoid conflict with user published migrations
        if (Schema::hasTable('sent_emails')) {
            return;
        }

        Schema::create('sent_emails', function (Blueprint $table): void {
            $table->increments('id');
            $table->date('date')->nullable();
            $table->string('from')->nullable();
            $table->text('to')->nullable();
            $table->text('cc')->nullable();
            $table->text('bcc')->nullable();
            $table->string('subject')->nullable();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_emails');
    }
};
