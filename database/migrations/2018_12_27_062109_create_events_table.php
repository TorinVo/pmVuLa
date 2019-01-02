<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('event_type', 16)->nullable();
            $table->string('bg_color', 16)->default('#83c340')->nullable();
            $table->string('repeat_type', 16)->nullable();
            $table->unsignedInteger('repeat_interval')->nullable();
            $table->string('repeat_days', 16)->nullable();
            $table->unsignedInteger('repeat_end')->nullable();;
            $table->unsignedInteger('repeat_limit')->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
