<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLine4choicesTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_4choices_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('line_basic_id');
            $table->integer('send_type');							//配信タイプ 0:即時配信　1:予約配信　2:毎日定時　3:毎週定時　4:登録から〇時間後　5:登録から〇日後〇時間後　99:配信タイプが決まってない状態で保存したとき
			$table->integer('send_status');							//配信状況 0:配信待ち　1:配信中　2:配信済　3:キャンセル　4:履歴を残さない　5:履歴を残さない場合の送信済　6:繰り返し
			$table->integer('send_count')->default(0);;				//配信数
			$table->string('push_title', 40)->nullable();
			$table->string('img_ratio', 10)->default('square');
			$table->string('img_size', 10)->default('cover');
			$table->string('img')->nullable();
            $table->dateTime('send_date')->nullable();				//配信日時
            $table->dateTime('reserve_send_date')->nullable();		//配信予定日時
            $table->unsignedBigInteger('sort_reserve_send_date')->nullable();	//ソート用の送信予定日
            $table->timestamps();

			$table->index('id', 'idx_id');
			$table->index('line_basic_id', 'idx_line_basic_id');
			$table->index('send_type', 'idx_send_type');
			$table->index('send_status', 'idx_send_status');
			$table->index('send_count', 'idx_send_count');
			$table->index('send_date', 'idx_send_date');
			$table->index('reserve_send_date', 'idx_reserve_send_date');
			$table->index('sort_reserve_send_date', 'idx_sort_reserve_send_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_4choices_templates');
    }
}
