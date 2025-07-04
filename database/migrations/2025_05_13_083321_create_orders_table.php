<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pembeli
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');
            $table->string('status')->default('pending');  // Status global order (pending, paid, shipped, completed, cancelled)
            $table->decimal('total_amount', 10, 2);  // Total seluruh pesanan
            $table->string('payment_method');
            $table->text('shipping_address');
            $table->timestamp('ordered_at')->nullable();
            $table->timestamps();
        });

    }


    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
