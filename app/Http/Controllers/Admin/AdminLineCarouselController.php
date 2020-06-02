<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Line_carousel_template;
use App\Model\Line_postback_template;
use Carbon\Carbon;
use Session;
use FormParts;
use Utility;
use DB;
use File;

class AdminLineCarouselController extends Controller
{
	public function __construct()
	{

	}

	public function index($channel_id, $id = "")
	{
		$db_data		 = [];
		$list_msg		 = [];
		$push_title		 = '';
		$reserve_date	 = '';
		$disp_data		 = [];

		if( !empty($id) ){
			$db_data = Line_carousel_template::join('line_official_accounts','line_official_accounts.line_basic_id', '=', 'line_carousel_templates.line_basic_id')->where('line_carousel_templates.line_basic_id', $channel_id)->where('line_carousel_templates.id', $id)->first();
			if( !empty($db_data) ){
				foreach($db_data as $lines){
					if( !empty($db_data->img1) ){
						$list_msg['img1'] = [$db_data->img1, $db_data->reserve_send_date, $db_data->title1, $db_data->text1, $db_data->act1, $db_data->label1, $db_data->value1];
					}
					if( !empty($db_data->img2) ){
						$list_msg['img2'] = [$db_data->img2, $db_data->reserve_send_date, $db_data->title2, $db_data->text2, $db_data->act2, $db_data->label2, $db_data->value2];
					}
					if( !empty($db_data->img3) ){
						$list_msg['img3'] = [$db_data->img3, $db_data->reserve_send_date, $db_data->title3, $db_data->text3, $db_data->act3, $db_data->label3, $db_data->value3];
					}
					if( !empty($db_data->img4) ){
						$list_msg['img4'] = [$db_data->img4, $db_data->reserve_send_date, $db_data->title4, $db_data->text4, $db_data->act4, $db_data->label4, $db_data->value4];
					}
					if( !empty($db_data->img5) ){
						$list_msg['img5'] = [$db_data->img5, $db_data->reserve_send_date, $db_data->title5, $db_data->text5, $db_data->act5, $db_data->label5, $db_data->value5];
					}
					if( !empty($db_data->img6) ){
						$list_msg['img6'] = [$db_data->img5, $db_data->reserve_send_date, $db_data->title6, $db_data->text6, $db_data->act6, $db_data->label6, $db_data->value6];
					}
					if( !empty($db_data->img7) ){
						$list_msg['img7'] = [$db_data->img7, $db_data->reserve_send_date, $db_data->title7, $db_data->text7, $db_data->act7, $db_data->label7, $db_data->value7];
					}
					if( !empty($db_data->img8) ){
						$list_msg['img8'] = [$db_data->img8, $db_data->reserve_send_date, $db_data->title8, $db_data->text8, $db_data->act8, $db_data->label8, $db_data->value8];
					}
					if( !empty($db_data->img9) ){
						$list_msg['img9'] = [$db_data->img9, $db_data->reserve_send_date, $db_data->title9, $db_data->text9, $db_data->act9, $db_data->label9, $db_data->value9];
					}
					if( !empty($db_data->img10) ){
						$list_msg['img10'] = [$db_data->img10, $db_data->reserve_send_date, $db_data->title10, $db_data->text10, $db_data->act10, $db_data->label10, $db_data->value10];
					}
				}
				$push_title = $db_data->push_title;
				$reserve_date = $db_data->reserve_send_date;
			}
		}

		$postback_options	 = '';
		$list_postback		 = [];

		//ポストバックデータ取得
		$postback_data = Line_postback_template::get();
		if( !empty($postback_data) ){
			foreach($postback_data as $lines){
				$list_postback[] = [$lines->postback, $lines->name];
			}
			$list_postback = $list_postback;
			$postback_options = FormParts::getMakeSelectOptions($list_postback, '');
		}

		//画面表示用配列
		$disp_data = [
			'list_postback'		=> $list_postback,
			'postback_options'	=> $postback_options,
			'push_title'		=> $push_title,
			'reserve_date'		=> $reserve_date,
			'channel_id'		=> $channel_id,
			'edit_id'			=> $id,
			'db_data'			=> $db_data,
			'list_msg'			=> $list_msg,
			'channel_id'		=> $channel_id,
			'redirect_url'		=> config('const.base_admin_url').'/member/line/setting/carousel/'.$channel_id,
			'save_redirect_url'	=> config('const.base_admin_url').'/member/line/setting/carousel/'.$channel_id,
			'ver'				=> time()
		];

		return view('admin.push.carousel', $disp_data);
	}

	/*
	 * 画像のアップロード処理
	 */
	public function uploadCarouselImgUpload(Request $request, $channel_id)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//landing_pagesテーブルに登録されているidを取得
		$id					 = $request->input('edit_id');
		$img				 = $request->input('msg');
		$send_type			 = $request->input('send_type');
		$reserve_date		 = $request->input('tmp_reserve_date');
		$tmp_regular_time	 = $request->input('tmp_regular_time');
		$img_name			 = $file->getClientOriginalName();
		$now_date			 = Carbon::now();
//error_log("$channel_id, $file, $id, $img, $send_type, $now_date, $reserve_date::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		try{
			$id = DB::transaction(function() use($channel_id, $file, $id, $img, $send_type, $now_date){
				//line_carousel_templatesテーブルにまだ登録されていないとき
				if( is_null($id) ){
					//最初の１回目の画像アップロードはinsert
					$id = Line_carousel_template::insertGetId([
						'line_basic_id'	=> $channel_id,
						'send_type'		=> $send_type,		//配信タイプ:
						'send_status'	=> 99,				//配信状況:99(送信前の保存)
						'send_count'	=> 0,
						$img			=> $img,
						'created_at'	=> $now_date,
						'updated_at'	=> $now_date
					]);
				}
//error_log("$channel_id, $file, $id, $img, ".$file->getClientOriginalExtension()."::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

				//画像名をid名にするためupdateを行う
				Line_carousel_template::where('id', $id)->update([
					'send_status'	=> 99,														//配信状況:99(送信前の保存)
					$img			=> $img.'_'.$id.'.'.$file->getClientOriginalExtension(),
				]);
//				throw new \Exception("テスト例外エラー");
				return $id;
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//画像の保存先を移動
		$file->move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img', $img.'_'.$id.'.'.$file->getClientOriginalExtension());

		//画像を管理画面上のプレビュー用にコピー
/*
		File::copy(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$img.'_'.$id.'.'.$file->getClientOriginalExtension(), 
			'/data/www/line/public/images/preview/'.$img.'_'.$id.'.'.$file->getClientOriginalExtension());
 */
		system('ln -s '.config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$img.'_'.$id.'.'.$file->getClientOriginalExtension().' /data/www/line/public/images/preview/');

		//更新データを取得
		$db_data = Line_carousel_template::where('id',$id)->first();

		if( $reserve_date != "" ){
			$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $reserve_date).'00';

		}else{
			//現在時刻をyyyymmddhhmmにフォーマット
			$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_upload'].",{$user['login_id']}");

		//失敗
		if( is_null($id) ){
			return false;

		//画像アップロード成功
		}else{
			return $id;
		}
	}

	/*
	 * 設定更新
	 */
	public function saveCarouselSetting(Request $request, $channel_id){
		$db_data = [];
//error_log(print_r($request->all(),true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		$validate = [
			'reserve_date'	 => 'bail|required|date_format_check|surrogate_pair_check|emoji_check',
			'push_title'	 => 'bail|required|surrogate_pair_check',
		];

		if( !empty($request->input('img1')) ){
			$validate['img1'] = 'bail|required|surrogate_pair_check';
			$validate['title1'] = 'bail|required|surrogate_pair_check';
			$validate['text1'] = 'bail|required|surrogate_pair_check';
			$validate['label1'] = 'bail|required|surrogate_pair_check';
			$validate['value1'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('img2')) ){
			$validate['img2'] = 'bail|required|surrogate_pair_check';
			$validate['title2'] = 'bail|required|surrogate_pair_check';
			$validate['text2'] = 'bail|required|surrogate_pair_check';
			$validate['label2'] = 'bail|required|surrogate_pair_check';
			$validate['value2'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('img3')) ){
			$validate['img3'] = 'bail|required|surrogate_pair_check';
			$validate['title3'] = 'bail|required|surrogate_pair_check';
			$validate['text3'] = 'bail|required|surrogate_pair_check';
			$validate['label3'] = 'bail|required|surrogate_pair_check';
			$validate['value3'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('img4')) ){
			$validate['img4'] = 'bail|required|surrogate_pair_check';
			$validate['title4'] = 'bail|required|surrogate_pair_check';
			$validate['text4'] = 'bail|required|surrogate_pair_check';
			$validate['label4'] = 'bail|required|surrogate_pair_check';
			$validate['value4'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('img5')) ){
			$validate['img5'] = 'bail|required|surrogate_pair_check';
			$validate['title5'] = 'bail|required|surrogate_pair_check';
			$validate['text5'] = 'bail|required|surrogate_pair_check';
			$validate['label5'] = 'bail|required|surrogate_pair_check';
			$validate['value5'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('img6')) ){
			$validate['img6'] = 'bail|required|surrogate_pair_check';
			$validate['title6'] = 'bail|required|surrogate_pair_check';
			$validate['text6'] = 'bail|required|surrogate_pair_check';
			$validate['label6'] = 'bail|required|surrogate_pair_check';
			$validate['value6'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('img7')) ){
			$validate['img7'] = 'bail|required|surrogate_pair_check';
			$validate['title7'] = 'bail|required|surrogate_pair_check';
			$validate['text7'] = 'bail|required|surrogate_pair_check';
			$validate['label7'] = 'bail|required|surrogate_pair_check';
			$validate['value7'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('img8')) ){
			$validate['img8'] = 'bail|required|surrogate_pair_check';
			$validate['title8'] = 'bail|required|surrogate_pair_check';
			$validate['text8'] = 'bail|required|surrogate_pair_check';
			$validate['label8'] = 'bail|required|surrogate_pair_check';
			$validate['value8'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('img9')) ){
			$validate['img9'] = 'bail|required|surrogate_pair_check';
			$validate['title9'] = 'bail|required|surrogate_pair_check';
			$validate['text9'] = 'bail|required|surrogate_pair_check';
			$validate['label9'] = 'bail|required|surrogate_pair_check';
			$validate['value9'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('img10')) ){
			$validate['img10'] = 'bail|required|surrogate_pair_check';
			$validate['title10'] = 'bail|required|surrogate_pair_check';
			$validate['text10'] = 'bail|required|surrogate_pair_check';
			$validate['label10'] = 'bail|required|surrogate_pair_check';
			$validate['value10'] = 'bail|required|surrogate_pair_check';
		}

		//予約配信
		if( $request->input('send_type') == 1 ){
			$db_data['reserve_send_date'] = $request->input('reserve_date');
			$db_data['sort_reserve_send_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('reserve_date')).'00';
		}

		//毎週配信
		if( $request->input('send_type') == 3 ){
			$db_data['send_week'] = $request->input('reserve_week');
			$validate['reserve_week'] = 'bail|required|surrogate_pair_check|emoji_check';
		}

		//毎日・毎週配信
		if( $request->input('send_type') == 2 || 
			$request->input('send_type') == 3 ){
			$db_data['send_regular_time'] = $request->input('regular_time');
			$validate['regular_time'] = 'bail|required|surrogate_pair_check|emoji_check';
		}

		//登録後配信
		if( $request->input('send_type') == 4 ){
			$db_data['send_after_day'] = $request->input('after_day');
			$db_data['send_regular_time'] = $request->input('regular_time');
			$validate['after_day'] = 'bail|required|surrogate_pair_check|emoji_check';
			$validate['regular_time'] = 'bail|required|surrogate_pair_check|emoji_check';
		}
		$this->validate($request, $validate);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_send'].",{$user['login_id']}");

		//現在時刻
		$now_date = Carbon::now();

		$line_basic_id = $request->input('channel_id');
		$line_push_id = $request->input('edit_id');
		$send_type = $request->input('send_type');

		if( empty($line_push_id) ){
			$db_data = array_merge($db_data, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,	
				'send_status'			=> $request->input('send_status'),				//送信状況:0(配信待ち)
				'send_count'			=> 0,											//送信数
				'push_title'			=> $request->input('push_title'),				//
				'img_ratio'				=> $request->input('img_ratio'),				//
				'img_size'				=> $request->input('img_size'),					//
				'title1'				=> $request->input('title1'),					//HTML内容
				'title2'				=> $request->input('title2'),					//HTML内容
				'title3'				=> $request->input('title3'),					//HTML内容
				'title4'				=> $request->input('title4'),					//HTML内容
				'title5'				=> $request->input('title5'),					//HTML内容
				'title6'				=> $request->input('title6'),					//HTML内容
				'title7'				=> $request->input('title7'),					//HTML内容
				'title8'				=> $request->input('title8'),					//HTML内容
				'title9'				=> $request->input('title9'),					//HTML内容
				'title10'				=> $request->input('title10'),					//HTML内容
				'img1'					=> $request->input('img1'),						//
				'img2'					=> $request->input('img2'),						//
				'img3'					=> $request->input('img3'),						//
				'img4'					=> $request->input('img4'),						//
				'img5'					=> $request->input('img5'),						//
				'img6'					=> $request->input('img6'),						//
				'img7'					=> $request->input('img7'),						//
				'img8'					=> $request->input('img8'),						//
				'img9'					=> $request->input('img9'),						//
				'img10'					=> $request->input('img10'),					//
				'text1'					=> $request->input('text1'),					//
				'text2'					=> $request->input('text2'),					//
				'text3'					=> $request->input('text3'),					//
				'text4'					=> $request->input('text4'),					//
				'text5'					=> $request->input('text5'),					//
				'text6'					=> $request->input('text6'),					//
				'text7'					=> $request->input('text7'),					//
				'text8'					=> $request->input('text8'),					//
				'text9'					=> $request->input('text9'),					//
				'text10'				=> $request->input('text10'),					//
				'act1'					=> $request->input('act1'),					//
				'act2'					=> $request->input('act2'),					//
				'act3'					=> $request->input('act3'),					//
				'act4'					=> $request->input('act4'),					//
				'act5'					=> $request->input('act5'),					//
				'act6'					=> $request->input('act6'),					//
				'act7'					=> $request->input('act7'),					//
				'act8'					=> $request->input('act8'),					//
				'act9'					=> $request->input('act9'),					//
				'act10'					=> $request->input('act10'),					//
				'label1'				=> $request->input('label1'),					//
				'label2'				=> $request->input('label2'),					//
				'label3'				=> $request->input('label3'),					//
				'label4'				=> $request->input('label4'),					//
				'label5'				=> $request->input('label5'),					//
				'label6'				=> $request->input('label6'),					//
				'label7'				=> $request->input('label7'),					//
				'label8'				=> $request->input('label8'),					//
				'label9'				=> $request->input('label9'),					//
				'label10'				=> $request->input('label10'),					//
				'value1'				=> $request->input('value1'),					//
				'value2'				=> $request->input('value2'),					//
				'value3'				=> $request->input('value3'),					//
				'value4'				=> $request->input('value4'),					//
				'value5'				=> $request->input('value5'),					//
				'value6'				=> $request->input('value6'),					//
				'value7'				=> $request->input('value7'),					//
				'value8'				=> $request->input('value8'),					//
				'value9'				=> $request->input('value9'),					//
				'value10'				=> $request->input('value10'),					//
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			$line_push_id = Line_carousel_template::insertGetId($db_data);
		}else{
			$db_data = array_merge($db_data, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,								//予約配信:1
				'send_status'			=> $request->input('send_status'),			//送信状況:0(配信待ち)
				'send_count'			=> 0,										//送信数
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			if( !empty($request->input('push_title')) ){
				$db_data['push_title'] = $request->input('push_title');
			}
			if( !empty($request->input('img_ratio')) ){
				$db_data['img_ratio'] = $request->input('img_ratio');
			}
			if( !empty($request->input('img_size')) ){
				$db_data['img_size'] = $request->input('img_size');
			}
			if( !empty($request->input('title1')) ){
				$db_data['title1'] = $request->input('title1');
			}
			if( !empty($request->input('title2')) ){
				$db_data['title2'] = $request->input('title2');
			}
			if( !empty($request->input('title3')) ){
				$db_data['title3'] = $request->input('title3');
			}
			if( !empty($request->input('title4')) ){
				$db_data['title4'] = $request->input('title4');
			}
			if( !empty($request->input('title5')) ){
				$db_data['title5'] = $request->input('title5');
			}
			if( !empty($request->input('title6')) ){
				$db_data['title6'] = $request->input('title6');
			}
			if( !empty($request->input('title7')) ){
				$db_data['title7'] = $request->input('title7');
			}
			if( !empty($request->input('title8')) ){
				$db_data['title8'] = $request->input('title8');
			}
			if( !empty($request->input('title9')) ){
				$db_data['title9'] = $request->input('title9');
			}
			if( !empty($request->input('title10')) ){
				$db_data['title10'] = $request->input('title10');
			}

			if( !empty($request->input('img1')) ){
				$db_data['img1'] = $request->input('img1');
			}
			if( !empty($request->input('img2')) ){
				$db_data['img2'] = $request->input('img2');
			}
			if( !empty($request->input('img3')) ){
				$db_data['img3'] = $request->input('img3');
			}
			if( !empty($request->input('img4')) ){
				$db_data['img4'] = $request->input('img4');
			}
			if( !empty($request->input('img5')) ){
				$db_data['img5'] = $request->input('img5');
			}
			if( !empty($request->input('img6')) ){
				$db_data['img6'] = $request->input('img6');
			}
			if( !empty($request->input('img7')) ){
				$db_data['img7'] = $request->input('img7');
			}
			if( !empty($request->input('img8')) ){
				$db_data['img8'] = $request->input('img8');
			}
			if( !empty($request->input('img9')) ){
				$db_data['img9'] = $request->input('img9');
			}
			if( !empty($request->input('img10')) ){
				$db_data['img10'] = $request->input('img10');
			}

			if( !empty($request->input('text1')) ){
				$db_data['text1'] = $request->input('text1');
			}
			if( !empty($request->input('text2')) ){
				$db_data['text2'] = $request->input('text2');
			}
			if( !empty($request->input('text3')) ){
				$db_data['text3'] = $request->input('text3');
			}
			if( !empty($request->input('text4')) ){
				$db_data['text4'] = $request->input('text4');
			}
			if( !empty($request->input('text5')) ){
				$db_data['text5'] = $request->input('text5');
			}
			if( !empty($request->input('text6')) ){
				$db_data['text6'] = $request->input('text6');
			}
			if( !empty($request->input('text7')) ){
				$db_data['text7'] = $request->input('text7');
			}
			if( !empty($request->input('text8')) ){
				$db_data['text8'] = $request->input('text8');
			}
			if( !empty($request->input('text9')) ){
				$db_data['text9'] = $request->input('text9');
			}
			if( !empty($request->input('text10')) ){
				$db_data['text10'] = $request->input('text10');
			}

			if( !empty($request->input('act1')) ){
				$db_data['act1'] = $request->input('act1');
			}
			if( !empty($request->input('act2')) ){
				$db_data['act2'] = $request->input('act2');
			}
			if( !empty($request->input('act3')) ){
				$db_data['act3'] = $request->input('act3');
			}
			if( !empty($request->input('act4')) ){
				$db_data['act4'] = $request->input('act4');
			}
			if( !empty($request->input('act5')) ){
				$db_data['act5'] = $request->input('act5');
			}
			if( !empty($request->input('act6')) ){
				$db_data['act6'] = $request->input('act6');
			}
			if( !empty($request->input('act7')) ){
				$db_data['act7'] = $request->input('act7');
			}
			if( !empty($request->input('act8')) ){
				$db_data['act8'] = $request->input('act8');
			}
			if( !empty($request->input('act9')) ){
				$db_data['act9'] = $request->input('act9');
			}
			if( !empty($request->input('act10')) ){
				$db_data['act10'] = $request->input('act10');
			}

			if( !empty($request->input('label1')) ){
				$db_data['label1'] = $request->input('label1');
			}
			if( !empty($request->input('label2')) ){
				$db_data['label2'] = $request->input('label2');
			}
			if( !empty($request->input('label3')) ){
				$db_data['label3'] = $request->input('label3');
			}
			if( !empty($request->input('label4')) ){
				$db_data['label4'] = $request->input('label4');
			}
			if( !empty($request->input('label5')) ){
				$db_data['label5'] = $request->input('label5');
			}
			if( !empty($request->input('label6')) ){
				$db_data['label6'] = $request->input('label6');
			}
			if( !empty($request->input('label7')) ){
				$db_data['label7'] = $request->input('label7');
			}
			if( !empty($request->input('label8')) ){
				$db_data['label8'] = $request->input('label8');
			}
			if( !empty($request->input('label9')) ){
				$db_data['label9'] = $request->input('label9');
			}
			if( !empty($request->input('label10')) ){
				$db_data['label10'] = $request->input('label10');
			}

			if( !empty($request->input('value1')) ){
				$db_data['value1'] = $request->input('value1');
			}
			if( !empty($request->input('value2')) ){
				$db_data['value2'] = $request->input('value2');
			}
			if( !empty($request->input('value3')) ){
				$db_data['value3'] = $request->input('value3');
			}
			if( !empty($request->input('value4')) ){
				$db_data['value4'] = $request->input('value4');
			}
			if( !empty($request->input('value5')) ){
				$db_data['value5'] = $request->input('value5');
			}
			if( !empty($request->input('value6')) ){
				$db_data['value6'] = $request->input('value6');
			}
			if( !empty($request->input('value7')) ){
				$db_data['value7'] = $request->input('value7');
			}
			if( !empty($request->input('value8')) ){
				$db_data['value8'] = $request->input('value8');
			}
			if( !empty($request->input('value9')) ){
				$db_data['value9'] = $request->input('value9');
			}
			if( !empty($request->input('value10')) ){
				$db_data['value10'] = $request->input('value10');
			}
			Line_carousel_template::where('id', $line_push_id)->update($db_data);
		}

		return $line_push_id;
	}

}
