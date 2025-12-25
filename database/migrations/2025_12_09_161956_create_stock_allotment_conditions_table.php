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
        Schema::create('stock_allotment_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            $table->decimal('land_extent_from', 8, 2);
            $table->decimal('land_extent_to', 8, 2);
            $table->integer('number_of_bags');
            $table->integer('at_a_time_how_many');
            $table->integer('number_of_times');
            $table->integer('interval_time_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure no overlapping ranges for the same district
            $table->index(['district_id', 'land_extent_from', 'land_extent_to'], 'sac_district_land_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_allotment_conditions');
    }
};
