<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_user_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('user_line_id');
			$table->String('name');
			$table->String('image')->nullable();
			$table->Text('message')->nullable();
            $table->timestamps();

			$table->unique('user_line_id');
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
        Schema::dropIfExists('line_user_profiles');
    }
}
