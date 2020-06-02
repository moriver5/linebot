<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackAccessLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track_access_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('env_data')->nullable();
			$table->String('script_name')->nullable();
			$table->String('line_basic_id');
			$table->String('user_line_id')->nullable();
			$table->String('csrf_token', 50)->nullable();
			$table->String('xuid', 30);
			$table->String('ad_cd',20)->nullable();
			$table->String('access_ip')->nullable();
			$table->String('access_ua')->nullable();
			$table->String('access_referrer')->nullable();
			$table->String('access_tag')->nullable();
			$table->Integer('status')->default(0);
			$table->String('image_unique')->nullable();
			$table->Integer('access_date');
            $table->timestamps();

			$table->index('line_basic_id', 'idx_line_basic_id');
			$table->index('user_line_id', 'idx_user_line_id');
			$table->index('status', 'idx_status');
			$table->index('xuid', 'idx_xuid');
			$table->index('ad_cd', 'idx_ad_cd');
			$table->index('csrf_token', 'idx_csrf_token');
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
        Schema::dropIfExists('track_access_logs');
    }
}
