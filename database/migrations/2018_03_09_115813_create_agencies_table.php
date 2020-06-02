<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name', 50);
            $table->string('login_id');
            $table->string('password')->nullable();
            $table->string('password_raw')->nullable();
            $table->rememberToken();
			$table->string('memo')->nullable();
            $table->timestamps();

			$table->unique('login_id');
			$table->index('id', 'idx_id');
			$table->index('login_id', 'idx_login_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agencies');
    }
}
