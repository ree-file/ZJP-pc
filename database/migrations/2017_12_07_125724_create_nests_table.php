<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kalnoy\Nestedset\NestedSet;

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
			$table->string('name')->unique();
			$table->integer('user_id')->unsigned();
			$table->boolean('is_selling')->default(false);
			$table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
			NestedSet::columns($table);
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
