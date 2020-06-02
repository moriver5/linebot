<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_codes', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('group_id')->nullable();
			$table->integer('asp_id')->default(0);
			$table->string('ad_cd');
			$table->integer('agency_id')->nullable();
			$table->string('login_id')->nullable();
			$table->string('password')->nullable();
            $table->string('category',30);
            $table->integer('aggregate_flg');
			$table->string('name')->nullable();
			$table->string('url')->nullable();
			$table->string('memo')->nullable();
			$table->timestamps();

			$table->unique('ad_cd');
			$table->index('ad_cd', 'idx_ad_cd');
			$table->index('name', 'idx_name');
			$table->index('url', 'idx_url');
			$table->index('category', 'idx_category');
			$table->index('aggregate_flg', 'idx_aggregate_flg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_codes');
    }
}
