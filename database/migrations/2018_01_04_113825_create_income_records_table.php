<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsiged();
            $table->enum('type', ['daily', 'bonus']);
            $table->integer('nest_id')->unsigned();
            $table->decimal('money_active', 10, 2)->unsigned()->nullable();
            $table->decimal('money_limit', 10, 2)->unsigned()->nullable();
            $table->decimal('coins', 10, 2)->unsigned()->nullable();
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
        Schema::dropIfExists('income_records');
    }
}
