<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('nest_id')->unsigned();
            $table->integer('eggs')->unsigned();
            $table->boolean('is_finished')->default(false);
			$table->date('cycle_date');
			$table->integer('frostB')->unsigned()->default(0);
			$table->integer('frostC')->unsigned()->default(0);
			$table->integer('from_weeks')->unsigned()->default(0);
			$table->integer('from_receivers')->unsigned()->default(0);
			$table->integer('from_community')->unsigned()->default(0);
			$table->integer('extracted_active')->unsigned()->default(0);
			$table->integer('extracted_limit')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
