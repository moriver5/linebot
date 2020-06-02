<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineOfficialAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_official_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('line_basic_id');
			$table->String('line_channel_id');
			$table->String('line_channel_secret');
			$table->Text('line_token');
			$table->String('name');
			$table->Text('memo');
			$table->String('qrcode')->nullable();
            $table->timestamps();

			$table->unique('line_basic_id');
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
        Schema::dropIfExists('line_official_accounts');
    }
}
