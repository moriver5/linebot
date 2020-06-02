<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminChannelAllowListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_channel_allow_lists', function (Blueprint $table) {
            $table->bigInteger('admin_id');
			$table->string('line_basic_id');
            $table->timestamps();

			$table->unique(['admin_id', 'line_basic_id']);
			$table->index('admin_id', 'idx_admin_id');
			$table->index('line_basic_id', 'idx_line_basic_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_channel_allow_lists');
    }
}
