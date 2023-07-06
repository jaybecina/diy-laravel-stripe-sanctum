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
        Schema::create('mood_boards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')
                ->unsigned()
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('frame_color');
            $table->longText('frame_background')->nullable();
            $table->longText('inspiration_picture')->nullable();
            $table->string('image_placeholder')->nullable();
            // $table->integer('version')->default(1);
            // $table->enum('status', array('Draft', 'Finished'))->default('Draft');
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
        Schema::dropIfExists('mood_boards');
    }
};
