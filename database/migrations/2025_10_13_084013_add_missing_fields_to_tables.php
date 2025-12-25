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
        // Add missing fields to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->string('name')->nullable()->after('document_number');
            $table->string('phone')->nullable()->after('name');
        });

        // Add missing fields to sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null')->after('user_id');
        });

        // Add missing fields to stores table
        Schema::table('stores', function (Blueprint $table) {
            $table->text('address')->nullable()->after('mandal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['name', 'phone']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
};