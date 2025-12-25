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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->foreignId('district_id')->constrained()->onDelete('cascade');
            $table->foreignId('mandal_id')->constrained()->onDelete('cascade');
            $table->decimal('total_land', 10, 2);
            $table->integer('total_stock_allotted')->default(0);
            $table->integer('stock_availed')->default(0);
            $table->string('document_photo')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
