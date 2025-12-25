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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->unique()->after('name');
            $table->foreignId('district_id')->nullable()->constrained()->onDelete('set null')->after('phone_number');
            $table->foreignId('mandal_id')->nullable()->constrained()->onDelete('set null')->after('district_id');
            $table->string('email')->nullable()->change();
            $table->boolean('must_change_password')->default(true)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'district_id', 'mandal_id', 'must_change_password']);
        });
    }
};
