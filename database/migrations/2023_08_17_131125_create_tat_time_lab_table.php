<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTatTimeLabTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tat_times.lab_tat_tat_time_lab', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id');
            $table->string('service_name', 255);
            $table->string('test_type', 255);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('days')->nullable();
            $table->time('report_delivery')->nullable();
            $table->boolean('status')->default(1);
            $table->string('cb', 255)->nullable();
            $table->timestamp('cd')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('ub', 255)->nullable();
            $table->timestamp('ud')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tat_times.lab_tat_tat_time_lab');
    }
}
