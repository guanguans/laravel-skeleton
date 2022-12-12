<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHttpLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('http_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('method', 10)->index('method');
            $table->string('path', 128)->index('path');
            $table->mediumText('request_header');
            $table->mediumText('input');
            $table->mediumText('response_header');
            $table->mediumText('response');
            $table->string('ip', 16)->index('ip');
            $table->string('duration', 10);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('http_log');
    }
}
