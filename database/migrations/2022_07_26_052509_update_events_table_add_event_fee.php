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
            $table->float('event_fee')->default(0)->after('event_schedule');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('events', 'event_fee'))
        {
            Schema::table('events', function($table)
            {
                $table->dropColumn('event_fee');
            });
        }
    }
};
