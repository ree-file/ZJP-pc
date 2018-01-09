<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invest_records', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('nest_id')->unsigned();
			$table->integer('contract_id')->unsigned();
			$table->enum('type', ['reinvest', 'upgrade', 'store']);
			$table->integer('eggs')->unsigned();
			$table->decimal('money', 10, 2)->unsigned();
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
        Schema::dropIfExists('invest_records');
    }
}
