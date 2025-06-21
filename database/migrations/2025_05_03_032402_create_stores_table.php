<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // satu user hanya bisa punya satu toko
            $table->string('name');
            $table->text('description');
            $table->string('logo')->nullable();
            $table->string('address');
            $table->string('phone');
            $table->string('status')->default('active'); 
            $table->timestamps();

            // Relasi ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stores');
    }
}

