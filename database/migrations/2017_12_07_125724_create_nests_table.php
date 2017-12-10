<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nests', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name');
			$table->integer('user_id')->unsigned();
			$table->integer('parent_id')->unsigned()->default(0);
			$table->integer('inviter_id')->unsigned()->default(0);
			$table->enum('community', ['A', 'B', 'C']);
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
        Schema::dropIfExists('nests');
    }
}
