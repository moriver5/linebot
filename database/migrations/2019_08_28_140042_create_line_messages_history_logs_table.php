<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineMessagesHistoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_messages_history_logs', function (Blueprint $table) {
            $table->integer('line_push_id');						//メッセージID
            $table->string('user_line_id');							//ＬＩＮＥ ID
			$table->integer('read_flg')->default(0);				//既読フラグ　未読：0 既読：1
			$table->dateTime('first_view_datetime')->nullable();	//最初にメッセージを観た日時
			$table->unsignedBigInteger('sort_date');				//ソートフラグ
            $table->timestamps();
			
			$table->unique(['line_push_id', 'user_line_id']);
			$table->index('line_push_id', 'idx_line_push_id');
			$table->index('user_line_id', 'idx_user_line_id');
			$table->index('read_flg', 'idx_read_flg');
			$table->index('sort_date', 'idx_sort_date');
			$table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_messages_history_logs');
    }
}
