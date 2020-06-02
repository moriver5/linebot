<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinePostbackTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_postback_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('line_basic_id');
			$table->integer('type');
			$table->string('name', 40)->nullable();
			$table->string('label')->nullable();
			$table->string('postback')->nullable();
            $table->text('msg1')->nullable();
            $table->text('msg2')->nullable();
            $table->text('msg3')->nullable();
            $table->text('msg4')->nullable();
            $table->text('msg5')->nullable();
            $table->timestamps();

			$table->index('id', 'idx_id');
			$table->index('line_basic_id', 'idx_line_basic_id');
			$table->index('type', 'idx_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_postback_templates');
    }
}
