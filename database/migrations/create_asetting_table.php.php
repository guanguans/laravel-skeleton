<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('asettings', function (Blueprint $table) {
            $table->id();

            $table->string('group')->default('general')->index();
            $table->enum('type', ['string', 'integer', 'boolean', 'json', 'array', 'date'])->default('string')->index();
            $table->string('key')->nullable(false)->index();
            $table->jsonb('value')->nullable(false);
            $table->string('title')->nullable(false);
            $table->boolean('is_visible')->default(true);
            $table->unique(['group', 'key']); // Bu satır eklenmiştir.

            $table->timestamps();
        });
    }
};
