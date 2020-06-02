<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('line_basic_id');
			$table->string('user_line_id');
            $table->Integer('follow_flg')->default(0);
            $table->Integer('block24h')->default(0);
            $table->Integer('block')->default(0);
            $table->Integer('disable')->default(0);
			$table->string('ad_cd')->nullable();
            $table->Integer('access_date');
            $table->timestamps();

			$table->unique(['line_basic_id', 'user_line_id']);
			$table->index('line_basic_id', 'idx_line_basic_id');
			$table->index('user_line_id', 'idx_user_line_id');
			$table->index('follow_flg', 'idx_follow_flg');
			$table->index('block24h', 'idx_block24h');
			$table->index('block', 'idx_block');
			$table->index('disable', 'idx_disable');
			$table->index('ad_cd', 'idx_ad_cd');
			$table->index('access_date', 'idx_access_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_users');
    }
}
