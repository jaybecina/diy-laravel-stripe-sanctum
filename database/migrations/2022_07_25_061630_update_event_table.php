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
        Schema::table('events', function($table)
        {
            $table->dropColumn(['event_date','event_time']);
            $table->dateTime('event_schedule')->after('link');
            $table->integer('event_type_id')->after('event_schedule')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('events', 'event_schedule'))
        {
            Schema::table('events', function($table)
            {
                $table->dropColumn('event_schedule');
            });
        }
        if (Schema::hasColumn('events', 'event_type_id'))
        {
            Schema::table('events', function($table)
            {
                $table->dropColumn('event_type_id');
            });
        }
    }
};
