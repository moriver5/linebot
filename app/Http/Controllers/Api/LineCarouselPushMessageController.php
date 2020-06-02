<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LineOfficialAccount;
use App\Model\Line_carousel_template;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

class LineCarouselPushMessageController extends Controller
{
	public function __construct()
	{

	}

	/*
	 *  カルーセルテンプレートを配信する
	 */
	public function sendLineCarouselTemplate($bot, $channel_id, $push_line_id, $list_line_id, $db_data = [])
	{
/*
		$line_account = LineOfficialAccount::where('line_basic_id', $channel_id)->first();

		//DBに登録チャンネルがないとき
		if( !isset($line_account->line_basic_id) ){
			return;
		}

		$bot = new LINEBot(
				new LINEBot\HTTPClient\CurlHTTPClient($line_account->line_token),
				['channelSecret' => $line_account->line_channel_secret]
			);
*/
		$list_column = [];
		if( preg_match("/(img\d+)_(\d+)\.(png|jpg|jpeg)/u", $db_data->img1, $image_id) > 0 ){
			//URIアクション
			if( $db_data->act1 == 1 ){
				$action = new UriTemplateActionBuilder($db_data->label1, $db_data->value1);

			//メッセージアクション
			}elseif( $db_data->act1 == 2 ){
				$action = new MessageTemplateActionBuilder($db_data->label1, $db_data->value1);

			//ポストバックアクション
			}elseif( $db_data->act1 == 3 ){
				$action = new PostbackTemplateActionBuilder($db_data->label1, $db_data->value1);				
			}
			$list_column[] = new CarouselColumnTemplateBuilder($db_data->title1, $db_data->text1, config('const.base_url')."/php/line/push/img/carousel/{$channel_id}/{$image_id[1]}/{$push_line_id}", [$action,]);
		}
		if( preg_match("/(img\d+)_(\d+)\.(png|jpg|jpeg)/u", $db_data->img2, $image_id) > 0 ){
			//URIアクション
			if( $db_data->act2 == 1 ){
				$action = new UriTemplateActionBuilder($db_data->label2, $db_data->value2);

			//メッセージアクション
			}elseif( $db_data->act2 == 2 ){
				$action = new MessageTemplateActionBuilder($db_data->label2, $db_data->value2);

			//ポストバックアクション
			}elseif( $db_data->act2 == 3 ){
				$action = new PostbackTemplateActionBuilder($db_data->label2, $db_data->value2);				
			}
			$list_column[] = new CarouselColumnTemplateBuilder($db_data->title2, $db_data->text2, config('const.base_url')."/php/line/push/img/carousel/{$channel_id}/{$image_id[1]}/{$push_line_id}", [$action,]);
		}
		if( preg_match("/(img\d+)_(\d+)\.(png|jpg|jpeg)/u", $db_data->img3, $image_id) > 0 ){
			//URIアクション
			if( $db_data->act3 == 1 ){
				$action = new UriTemplateActionBuilder($db_data->label3, $db_data->value3);

			//メッセージアクション
			}elseif( $db_data->act3 == 2 ){
				$action = new MessageTemplateActionBuilder($db_data->label3, $db_data->value3);

			//ポストバックアクション
			}elseif( $db_data->act3 == 3 ){
				$action = new PostbackTemplateActionBuilder($db_data->label3, $db_data->value3);				
			}
			$list_column[] = new CarouselColumnTemplateBuilder($db_data->title3, $db_data->text3, config('const.base_url')."/php/line/push/img/carousel/{$channel_id}/{$image_id[1]}/{$push_line_id}", [$action,]);
		}
		if( preg_match("/(img\d+)_(\d+)\.(png|jpg|jpeg)/u", $db_data->img4, $image_id) > 0 ){
			//URIアクション
			if( $db_data->act4 == 1 ){
				$action = new UriTemplateActionBuilder($db_data->label4, $db_data->value4);

			//メッセージアクション
			}elseif( $db_data->act4 == 2 ){
				$action = new MessageTemplateActionBuilder($db_data->label4, $db_data->value4);

			//ポストバックアクション
			}elseif( $db_data->act4 == 3 ){
				$action = new PostbackTemplateActionBuilder($db_data->label4, $db_data->value4);				
			}
			$list_column[] = new CarouselColumnTemplateBuilder($db_data->title4, $db_data->text4, config('const.base_url')."/php/line/push/img/carousel/{$channel_id}/{$image_id[1]}/{$push_line_id}", [$action,]);
		}
		if( preg_match("/(img\d+)_(\d+)\.(png|jpg|jpeg)/u", $db_data->img5, $image_id) > 0 ){
			//URIアクション
			if( $db_data->act5 == 1 ){
				$action = new UriTemplateActionBuilder($db_data->label5, $db_data->value5);

			//メッセージアクション
			}elseif( $db_data->act5 == 2 ){
				$action = new MessageTemplateActionBuilder($db_data->label5, $db_data->value5);

			//ポストバックアクション
			}elseif( $db_data->act5 == 3 ){
				$action = new PostbackTemplateActionBuilder($db_data->label5, $db_data->value5);				
			}
			$list_column[] = new CarouselColumnTemplateBuilder($db_data->title5, $db_data->text5, config('const.base_url')."/php/line/push/img/carousel/{$channel_id}/{$image_id[1]}/{$push_line_id}", [$action,]);
		}
		if( preg_match("/(img\d+)_(\d+)\.(png|jpg|jpeg)/u", $db_data->img6, $image_id) > 0 ){
			//URIアクション
			if( $db_data->act6 == 1 ){
				$action = new UriTemplateActionBuilder($db_data->label6, $db_data->value6);

			//メッセージアクション
			}elseif( $db_data->act6 == 2 ){
				$action = new MessageTemplateActionBuilder($db_data->label6, $db_data->value6);

			//ポストバックアクション
			}elseif( $db_data->act6 == 3 ){
				$action = new PostbackTemplateActionBuilder($db_data->label6, $db_data->value6);				
			}
			$list_column[] = new CarouselColumnTemplateBuilder($db_data->title6, $db_data->text6, config('const.base_url')."/php/line/push/img/carousel/{$channel_id}/{$image_id[1]}/{$push_line_id}", [$action,]);
		}
		if( preg_match("/(img\d+)_(\d+)\.(png|jpg|jpeg)/u", $db_data->img7, $image_id) > 0 ){
			//URIアクション
			if( $db_data->act7 == 1 ){
				$action = new UriTemplateActionBuilder($db_data->label7, $db_data->value7);

			//メッセージアクション
			}elseif( $db_data->act7 == 2 ){
				$action = new MessageTemplateActionBuilder($db_data->label7, $db_data->value7);

			//ポストバックアクション
			}elseif( $db_data->act7 == 3 ){
				$action = new PostbackTemplateActionBuilder($db_data->label7, $db_data->value7);				
			}
			$list_column[] = new CarouselColumnTemplateBuilder($db_data->title7, $db_data->text7, config('const.base_url')."/php/line/push/img/carousel/{$channel_id}/{$image_id[1]}/{$push_line_id}", [$action,]);
		}
		if( preg_match("/(img\d+)_(\d+)\.(png|jpg|jpeg)/u", $db_data->img8, $image_id) > 0 ){
			//URIアクション
			if( $db_data->act8 == 1 ){
				$action = new UriTemplateActionBuilder($db_data->label8, $db_data->value8);

			//メッセージアクション
			}elseif( $db_data->act8 == 2 ){
				$action = new MessageTemplateActionBuilder($db_data->label8, $db_data->value8);

			//ポストバックアクション
			}elseif( $db_data->act8 == 3 ){
				$action = new PostbackTemplateActionBuilder($db_data->label8, $db_data->value8);				
			}
			$list_column[] = new CarouselColumnTemplateBuilder($db_data->title8, $db_data->text8, config('const.base_url')."/php/line/push/img/carousel/{$channel_id}/{$image_id[1]}/{$push_line_id}", [$action,]);
		}
		if( preg_match("/(img\d+)_(\d+)\.(png|jpg|jpeg)/u", $db_data->img9, $image_id) > 0 ){
			//URIアクション
			if( $db_data->act9 == 1 ){
				$action = new UriTemplateActionBuilder($db_data->label9, $db_data->value9);

			//メッセージアクション
			}elseif( $db_data->act9 == 2 ){
				$action = new MessageTemplateActionBuilder($db_data->label9, $db_data->value9);

			//ポストバックアクション
			}elseif( $db_data->act9 == 3 ){
				$action = new PostbackTemplateActionBuilder($db_data->label9, $db_data->value9);				
			}
			$list_column[] = new CarouselColumnTemplateBuilder($db_data->title9, $db_data->text9, config('const.base_url')."/php/line/push/img/carousel/{$channel_id}/{$image_id[1]}/{$push_line_id}", [$action,]);
		}
		if( preg_match("/(img\d+)_(\d+)\.(png|jpg|jpeg)/u", $db_data->img10, $image_id) > 0 ){
			//URIアクション
			if( $db_data->act10 == 1 ){
				$action = new UriTemplateActionBuilder($db_data->label10, $db_data->value10);

			//メッセージアクション
			}elseif( $db_data->act10 == 2 ){
				$action = new MessageTemplateActionBuilder($db_data->label10, $db_data->value10);

			//ポストバックアクション
			}elseif( $db_data->act10 == 3 ){
				$action = new PostbackTemplateActionBuilder($db_data->label10, $db_data->value10);				
			}
			$list_column[] = new CarouselColumnTemplateBuilder($db_data->title10, $db_data->text10, config('const.base_url')."/php/line/push/img/carousel/{$channel_id}/{$image_id[1]}/{$push_line_id}", [$action,]);
		}

		// カラムの配列を組み合わせてカルーセルを作成する
		$carousel = new CarouselTemplateBuilder($list_column, $db_data->img_ratio, $db_data->img_size);

		// カルーセルを追加してメッセージを作る
		$carousel_message = new TemplateMessageBuilder($db_data->push_title, $carousel);

		$messageBuilder = new MultiMessageBuilder();
		$messageBuilder->add($carousel_message);
		$response = $bot->multicast($list_line_id, $messageBuilder, true);

		//エラー
		if( !$response->isSucceeded() ){
			error_log('Failed! '. $response->getRawBody(),3,"/data/www/line/storage/logs/nishi_log.txt");
		}
	}

}
