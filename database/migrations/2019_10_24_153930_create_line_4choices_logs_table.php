<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLine4choicesLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_4choices_logs', function (Blueprint $table) {
			$table->bigIncrements('id');
            $table->integer('master_id');					//line_2choice_templatesテーブルのid
			$table->string('msg');							//シナリオメッセージ
			$table->integer('act1');						//act1の選択肢を押したときのアクションID
			$table->string('label1');						//act1の選択肢のラベル
			$table->string('value1')->nullable();;			//act1の選択肢を押したときのアクション
			$table->integer('act2');						//act2の選択肢を押したときのアクションID
			$table->string('label2');						//act2の選択肢のラベル
			$table->string('value2')->nullable();;			//act2の選択肢を押したときのアクション
			$table->integer('act3');						//act3の選択肢を押したときのアクションID
			$table->string('label3');						//act3の選択肢のラベル
			$table->string('value3')->nullable();;			//act3の選択肢を押したときのアクション
			$table->integer('act4');						//act4の選択肢を押したときのアクションID
			$table->string('label4');						//act4の選択肢のラベル
			$table->string('value4')->nullable();;			//act4の選択肢を押したときのアクション
            $table->timestamps();

//			$table->unique('master_id');
			$table->index('id', 'idx_id');
			$table->index('master_id', 'idx_master_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_4choices_logs');
    }
}
