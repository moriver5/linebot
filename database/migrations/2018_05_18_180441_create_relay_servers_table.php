<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelayServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relay_servers', function (Blueprint $table) {
			$table->string('type',10);
			$table->string('ip',15)->nullable();
			$table->integer('port')->nullable();
            $table->timestamps();

			$table->unique('type');
			$table->index('type', 'idx_type');
			$table->index('port', 'idx_port');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relay_servers');
    }
}
