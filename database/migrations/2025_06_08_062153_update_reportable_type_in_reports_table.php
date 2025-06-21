<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateReportableTypeInReportsTable extends Migration
{
    public function up()
    {
        DB::table('reports')->where('reportable_type', 'App\Models\Book')->update(['reportable_type' => 'book']);
        DB::table('reports')->where('reportable_type', 'App\Models\User')->update(['reportable_type' => 'user']);
        DB::table('reports')->where('reportable_type', 'App\Models\Order')->update(['reportable_type' => 'order']);
    }

    public function down()
    {
        DB::table('reports')->where('reportable_type', 'book')->update(['reportable_type' => 'App\Models\Book']);
        DB::table('reports')->where('reportable_type', 'user')->update(['reportable_type' => 'App\Models\User']);
        DB::table('reports')->where('reportable_type', 'order')->update(['reportable_type' => 'App\Models\Order']);
    }
}
