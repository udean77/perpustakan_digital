<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id'); // Session ID untuk tracking
            $table->string('intent_type')->nullable(); // Jenis intent (book_search, redeem_code, etc)
            $table->string('query_type')->nullable(); // Tipe query (category, author, price, etc)
            $table->boolean('was_helpful')->nullable(); // Feedback dari user
            $table->integer('response_time_ms')->nullable(); // Waktu respons dalam milidetik
            $table->string('user_agent')->nullable(); // Browser/device info
            $table->string('ip_address')->nullable(); // IP address
            $table->timestamp('started_at'); // Waktu mulai chat
            $table->timestamp('ended_at')->nullable(); // Waktu selesai chat
            $table->integer('message_count')->default(0); // Jumlah pesan dalam session
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_analytics');
    }
}; 