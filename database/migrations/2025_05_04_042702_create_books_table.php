<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            // Relasi ke user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Tambahkan store_id sebagai foreign key
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('set null');

            $table->string('title');
            $table->string('author');
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->string('category')->nullable();  
            $table->text('description');
            $table->enum('book_type', ['physical', 'ebook']);
            $table->string('cover'); // path ke file sampul
            $table->string('ebook_file')->nullable(); // jika e-book
            $table->string('physical_book_file')->nullable(); // jika buku fisik
            $table->string('publisher')->nullable(); // Add the publisher column here
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
