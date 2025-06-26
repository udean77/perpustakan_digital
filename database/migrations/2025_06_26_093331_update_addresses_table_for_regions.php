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
        Schema::table('addresses', function (Blueprint $table) {
            // Add new columns for structured address
            $table->unsignedInteger('province_id')->after('alamat_lengkap')->nullable();
            $table->unsignedInteger('city_id')->after('province_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn(['province_id', 'city_id']);
        });
    }
};
