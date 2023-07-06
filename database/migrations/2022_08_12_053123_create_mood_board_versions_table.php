<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mood_board_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mood_board_id');
            $table->foreign('mood_board_id')->references('id')->on('mood_boards');
            $table->integer('version');
            $table->longText('remarks')->nullable();
            $table->enum('status',['draft','approved']);
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
        Schema::dropIfExists('mood_board_versions');
    }
};
