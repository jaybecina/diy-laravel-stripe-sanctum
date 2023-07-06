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
        Schema::create('mood_board_version_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mood_board_version_id');
            $table->foreign('mood_board_version_id')->references('id')->on('mood_board_versions');
            $table->unsignedBigInteger('mood_board_item_id');
            $table->foreign('mood_board_item_id')->references('id')->on('mood_board_items');
            $table->float('x',30,20)->default(0);
            $table->float('y',30,20)->default(0);
            $table->float('h',30,20)->default(20);
            $table->float('w',30,20)->default(20);
            $table->integer('index')->default(0);
            $table->longText('remarks')->nullable();
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
        Schema::dropIfExists('mood_board_version_items');
    }
};
