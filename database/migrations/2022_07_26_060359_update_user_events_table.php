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
        Schema::table('user_events', function($table)
        {
            $table->float('amount_paid')->default(0)->after('is_booked');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_events', 'amount_paid'))
        {
            Schema::table('user_events', function($table)
            {
                $table->dropColumn('amount_paid');
            });
        }
    }
};
