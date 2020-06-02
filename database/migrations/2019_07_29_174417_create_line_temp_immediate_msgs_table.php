<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineTempImmediateMsgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_temp_immediate_msgs', function (Blueprint $table) {
            $table->integer('line_push_id');					//配信ID
            $table->string('user_line_id');						//usersテーブルのid
			$table->timestamps();

			$table->unique(['line_push_id', 'user_line_id']);
			$table->index('line_push_id', 'idx_line_push_id');
			$table->index('user_line_id', 'idx_user_line_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_temp_immediate_msgs');
    }
}
