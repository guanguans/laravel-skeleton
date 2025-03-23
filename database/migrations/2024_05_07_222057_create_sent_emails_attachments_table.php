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

use Dcblogdev\LaravelSentEmails\Models\SentEmail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sent_emails_attachments', function (Blueprint $table): void {
            $table->increments('id');
            $table->foreignIdFor(SentEmail::class)->nullable();
            $table->string('filename')->nullable();
            $table->text('path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_emails_attachments');
    }
};
