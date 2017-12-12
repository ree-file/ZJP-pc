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
			$table->integer('frostB');
			$table->integer('frostC');
			$table->integer('from_weeks')->unsigned();
			$table->integer('from_receivers')->unsigned();
			$table->integer('from_community')->unsigned();
			$table->integer('extracted_active')->unsigned();
			$table->integer('extracted_limit')->unsigned();
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
