<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackImageLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track_image_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('env_data')->nullable();
			$table->String('script_name')->nullable();
			$table->String('line_basic_id')->nullable();
			$table->String('user_line_id')->nullable();
			$table->String('access_ip')->nullable();
			$table->String('access_ua')->nullable();
			$table->String('image_unique')->nullable();
            $table->timestamps();

			$table->index('line_basic_id', 'idx_line_basic_id');
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
        Schema::dropIfExists('track_image_logs');
    }
}
