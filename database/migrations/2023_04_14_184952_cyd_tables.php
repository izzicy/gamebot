<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('choose_your_door')->create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('channel_id', 18);
            $table->string('last_message_id', 20)->nullable();

            $table->tinyInteger('last_door_count')->nullable();

            $table->timestamps();

            $table->unique('channel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('choose_your_door')->dropIfExists('games');
    }
};
