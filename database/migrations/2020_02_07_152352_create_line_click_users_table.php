<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineClickUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_click_users', function (Blueprint $table) {
            $table->integer('line_push_id');						//メッセージID
            $table->string('line_basic_id');						//BASIC ID
            $table->string('user_line_id');							//ＬＩＮＥ ID
            $table->string('short_url');							//ショートURL
            $table->string('url');									//元のURL
			$table->integer('read')->default(0);					//開封フラグ　未開封：0 開封：1
			$table->integer('click')->default(0);					//クリックフラグ　未クリック：0 クリック：1
			$table->unsignedBigInteger('sort_date');				//ソートフラグ
            $table->timestamps();
			
			$table->unique(['line_push_id', 'user_line_id', 'short_url']);
			$table->index('line_push_id', 'idx_line_push_id');
			$table->index('line_basic_id', 'idx_line_basic_id');
			$table->index('user_line_id', 'idx_user_line_id');
			$table->index('short_url', 'idx_short_url');
			$table->index('read', 'idx_read');
			$table->index('click', 'idx_click');
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
        Schema::dropIfExists('line_click_users');
    }
}
