<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineUserMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_user_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->String('line_basic_id');
			$table->String('user_line_id');
			$table->String('act_flg');
			$table->String('reply_token');
			$table->String('msg');
            $table->timestamps();

			$table->index('line_basic_id', 'idx_line_basic_id');
			$table->index('user_line_id', 'idx_user_line_id');
			$table->index('act_flg', 'idx_act_flg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_user_messages');
    }
}
