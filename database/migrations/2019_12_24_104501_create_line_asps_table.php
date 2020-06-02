<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineAspsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_asps', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('asp',20);
			$table->string('kickback_url');
            $table->timestamps();

			$table->index('id', 'idx_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_asps');
    }
}
