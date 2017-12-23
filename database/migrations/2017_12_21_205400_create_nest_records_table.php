<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNestRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nest_records', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['week_got', 'community_got', 'invite_got', 'extract', 'reinvest', 'upgrade']);
            $table->integer('nest_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('contract_id')->unsigned();
            $table->integer('eggs')->unsigned();
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
        Schema::dropIfExists('nest_records');
    }
}
