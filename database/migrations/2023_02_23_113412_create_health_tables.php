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
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\ResultStores\EloquentHealthResultStore;

return new class extends Migration {
    public function up(): void
    {
        $connection = (new HealthCheckResultHistoryItem)->getConnectionName();
        $tableName = EloquentHealthResultStore::getHistoryItemInstance()->getTable();

        Schema::connection($connection)->create($tableName, function (Blueprint $table): void {
            $table->id();

            $table->string('check_name');
            $table->string('check_label');
            $table->string('status');
            $table->text('notification_message')->nullable();
            $table->string('short_summary')->nullable();
            $table->json('meta');
            $table->timestamp('ended_at');
            $table->uuid('batch');

            $table->timestamps();
        });

        Schema::connection($connection)->table($tableName, function (Blueprint $table): void {
            $table->index('created_at');
            $table->index('batch');
        });
    }
};
