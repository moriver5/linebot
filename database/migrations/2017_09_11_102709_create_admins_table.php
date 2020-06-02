<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
//            $table->string('name')->unique();
            $table->string('email');
            $table->string('password')->nullable();
            $table->integer('type')->default('0');
            $table->rememberToken();
            $table->string('access_host', 50)->nullable();
            $table->string('user_agent')->nullable();
			$table->dateTime('last_login_date')->nullable();
            $table->timestamps();
			
			$table->index('email', 'idx_email');
			$table->index('type', 'idx_type');
			$table->index('last_login_date', 'idx_last_login_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
		Schema::dropIfExists('admins');
    }
}
