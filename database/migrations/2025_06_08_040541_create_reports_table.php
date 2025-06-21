<?php

// database/migrations/xxxx_xx_xx_create_reports_table.php

// database/migrations/2025_06_08_000000_create_reports_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // pelapor
            $table->string('reportable_type');
            $table->unsignedBigInteger('reportable_id'); // id target
            $table->text('reason');
            $table->enum('status', ['pending', 'process', 'resolved'])->default('pending');
            $table->timestamps();

            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
