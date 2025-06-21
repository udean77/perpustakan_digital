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
        Schema::create('redeem_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['discount', 'cashback', 'free_shipping']);
            $table->decimal('value', 10, 2);
            $table->enum('value_type', ['percentage', 'fixed']);
            $table->integer('max_usage')->default(1);
            $table->integer('used_count')->default(0);
            $table->decimal('min_purchase', 10, 2)->nullable();
            $table->date('valid_from');
            $table->date('valid_until');
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redeem_codes');
    }
};
