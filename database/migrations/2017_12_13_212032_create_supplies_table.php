<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->increments('id');
			$table->enum('type', ['save', 'get']);
			$table->integer('user_id')->unsigned();
			$table->string('card_number');
			$table->decimal('money', 10,2)->unsigned();
			$table->string('message')->default('');
			$table->string('image')->nullable();
			$table->enum('status', ['processing', 'accepted', 'rejected'])->default('processing');
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
        Schema::dropIfExists('supplies');
    }
}
