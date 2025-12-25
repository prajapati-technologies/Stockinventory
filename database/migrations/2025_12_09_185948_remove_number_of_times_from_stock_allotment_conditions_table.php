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
        Schema::table('stock_allotment_conditions', function (Blueprint $table) {
            $table->dropColumn('number_of_times');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_allotment_conditions', function (Blueprint $table) {
            $table->integer('number_of_times')->after('at_a_time_how_many');
        });
    }
};
