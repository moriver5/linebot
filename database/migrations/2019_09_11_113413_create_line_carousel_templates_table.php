<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineCarouselTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_carousel_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('line_basic_id');
            $table->integer('send_type');							//配信タイプ 0:即時配信　1:予約配信　2:毎日定時　3:毎週定時　4:登録から〇時間後　5:登録から〇日後〇時間後　99:配信タイプが決まってない状態で保存したとき
			$table->integer('send_status');							//配信状況 0:配信待ち　1:配信中　2:配信済　3:キャンセル　4:履歴を残さない　5:履歴を残さない場合の送信済　6:繰り返し
			$table->integer('send_count')->default(0);;				//配信数
			$table->string('img_ratio', 10)->default('square');
			$table->string('img_size', 10)->default('cover');
			$table->string('push_title', 40)->nullable();

			$table->string('title1', 40)->nullable();
 			$table->string('img1')->nullable();
 			$table->string('text1', 120)->nullable();
 			$table->integer('act1')->nullable();
 			$table->string('label1', 20)->nullable();
 			$table->text('value1')->nullable();

			$table->string('title2', 40)->nullable();
 			$table->string('img2')->nullable();
 			$table->string('text2', 120)->nullable();
 			$table->integer('act2')->nullable();
 			$table->string('label2', 20)->nullable();
 			$table->text('value2')->nullable();

			$table->string('title3', 40)->nullable();
 			$table->string('img3')->nullable();
 			$table->string('text3', 120)->nullable();
 			$table->integer('act3')->nullable();
 			$table->string('label3', 20)->nullable();
 			$table->text('value3')->nullable();

			$table->string('title4', 40)->nullable();
 			$table->string('img4')->nullable();
 			$table->string('text4', 120)->nullable();
 			$table->integer('act4')->nullable();
 			$table->string('label4', 20)->nullable();
 			$table->text('value4')->nullable();

			$table->string('title5', 40)->nullable();
 			$table->string('img5')->nullable();
 			$table->string('text5', 120)->nullable();
 			$table->integer('act5')->nullable();
 			$table->string('label5', 20)->nullable();
 			$table->text('value5')->nullable();

			$table->string('title6', 40)->nullable();
 			$table->string('img6')->nullable();
 			$table->string('text6', 120)->nullable();
 			$table->integer('act6')->nullable();
 			$table->string('label6', 20)->nullable();
 			$table->text('value6')->nullable();

			$table->string('title7', 40)->nullable();
 			$table->string('img7')->nullable();
 			$table->string('text7', 120)->nullable();
 			$table->integer('act7')->nullable();
 			$table->string('label7', 20)->nullable();
 			$table->text('value7')->nullable();

			$table->string('title8', 40)->nullable();
 			$table->string('img8')->nullable();
 			$table->string('text8', 120)->nullable();
 			$table->integer('act8')->nullable();
 			$table->string('label8', 20)->nullable();
 			$table->text('value8')->nullable();

			$table->string('title9', 40)->nullable();
 			$table->string('img9')->nullable();
 			$table->string('text9', 120)->nullable();
 			$table->integer('act9')->nullable();
 			$table->string('label9', 20)->nullable();
 			$table->text('value9')->nullable();

			$table->string('title10', 40)->nullable();
 			$table->string('img10')->nullable();
 			$table->string('text10', 120)->nullable();
 			$table->integer('act10')->nullable();
 			$table->string('label10', 20)->nullable();
 			$table->text('value10')->nullable();

            $table->string('send_regular_time', 5)->nullable();		//定時配信時刻
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
        Schema::dropIfExists('line_carousel_templates');
    }
}
