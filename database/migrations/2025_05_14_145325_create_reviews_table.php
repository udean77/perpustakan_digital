<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // user yang mengulas
            $table->foreignId('book_id')->constrained()->onDelete('cascade'); // buku yang diulas
            $table->tinyInteger('rating'); // rating dari 1–5
            $table->text('comment')->nullable(); // komentar opsional
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};

