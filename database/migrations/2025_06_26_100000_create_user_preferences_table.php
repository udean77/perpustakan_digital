<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('preferred_categories')->nullable(); // Array kategori favorit
            $table->json('preferred_authors')->nullable(); // Array penulis favorit
            $table->decimal('min_price', 10, 2)->nullable(); // Harga minimum
            $table->decimal('max_price', 10, 2)->nullable(); // Harga maksimum
            $table->enum('preferred_book_type', ['physical', 'ebook', 'both'])->default('both');
            $table->integer('min_rating')->default(0); // Rating minimum yang diinginkan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_preferences');
    }
}; 