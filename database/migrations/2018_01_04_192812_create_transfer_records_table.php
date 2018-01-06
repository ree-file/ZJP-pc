<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_records', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('payer_id')->unsigned();
			$table->integer('receiver_id')->unsigned();
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
        Schema::dropIfExists('transfer_records');
    }
}
