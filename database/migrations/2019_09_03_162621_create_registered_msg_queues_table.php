<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegisteredMsgQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registered_msg_queues', function (Blueprint $table) {
			$table->integer('line_push_id');						//メッセージID
			$table->string('user_line_id');							//ＬＩＮＥ ID
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
        Schema::dropIfExists('registered_msg_queues');
    }
}
