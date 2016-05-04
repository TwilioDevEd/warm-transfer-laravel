<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActiveCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'active_calls', function (Blueprint $table) {
                $table->increments('id');
                $table->string('agent_id');
                $table->string('conference_id');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('active_calls', function (Blueprint $table) {
            //
        });
    }
}
