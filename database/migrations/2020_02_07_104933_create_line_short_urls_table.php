<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineShortUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_short_urls', function (Blueprint $table) {
//            $table->bigIncrements('id');
			$table->string('url',191);
			$table->string('short_url',15);
			$table->integer('click')->default(0);
            $table->timestamps();

			$table->unique('url');
			$table->index('url', 'idx_url');
			$table->index('short_url', 'idx_short_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_short_urls');
    }
}
