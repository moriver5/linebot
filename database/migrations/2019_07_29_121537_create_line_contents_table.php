<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('line_basic_id');
			$table->integer('type');
            $table->text('msg1')->nullable();
            $table->text('msg2')->nullable();
            $table->text('msg3')->nullable();
            $table->text('msg4')->nullable();
            $table->text('msg5')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_contents');
    }
}
