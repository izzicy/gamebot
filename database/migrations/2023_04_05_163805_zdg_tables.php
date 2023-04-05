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
        Schema::connection('zero_dollar_game')->create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('channel_id', 18);
            $table->integer('width');
            $table->integer('height');
            $table->timestamps();

            $table->unique('channel_id');
        });

        Schema::connection('zero_dollar_game')->create('pixels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('game_id');

            $table->string('user_id', 18);

            $table->tinyInteger('posx')->unsigned();
            $table->tinyInteger('posy')->unsigned();

            $table->tinyInteger('r')->unsigned();
            $table->tinyInteger('g')->unsigned();
            $table->tinyInteger('b')->unsigned();

            $table->timestamp('created_at');

            $table->index(['game_id', 'posx', 'posy', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('zero_dollar_game')->dropIfExists('games');
        Schema::connection('zero_dollar_game')->dropIfExists('pixels');
    }
};
