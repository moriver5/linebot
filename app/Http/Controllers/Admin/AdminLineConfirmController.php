<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Line_2choices_log;
use App\Model\Line_2choices_template;
use App\Model\Line_4choices_log;
use App\Model\Line_4choices_template;
use App\Model\Line_postback_template;
use Carbon\Carbon;
use Session;
use Utility;
use FormParts;
use DB;
use File;

class AdminLineConfirmController extends Controller
{
	public function __construct()
	{

	}

	public function index($channel_id, $id = "")
	{
		$disp_data			 = [];
		$db_data			 = [];
		$list_scenario		 = [];
		$list_postback		 = [];
		$list_msg			 = [];
		$push_title			 = '';
		$reserve_date		 = '';
		$send_status		 = '';
		$scenario_options	 = '';
		$postback_options	 = '';

		if( !empty($id) ){
			//シナリオデータ取得
			$db_data = Line_2choices_template::select('line_2choices_templates.push_title', 'line_2choices_templates.send_status', 'line_2choices_templates.reserve_send_date', 'line_2choices_templates.reserve_send_date', 'line_2choices_logs.*')->join('line_2choices_logs', 'line_2choices_templates.id', '=', 'line_2choices_logs.master_id')->where('line_2choices_templates.id', $id)->orderBy('line_2choices_logs.id')->get();
//error_log(print_r($db_data,true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

			if( !empty($db_data) ){
				foreach($db_data as $lines){
					$list_scenario[]	 = ['master_id='.$id.'&'.config('const.scenario_val_prefix').$lines->id, $lines->id];
					$send_status		 = $lines->send_status;
					$push_title			 = $lines->push_title;
					$reserve_date		 = $lines->reserve_send_date;

					if( !empty($db_data->msg) ){
						$list_msg[] = [$db_data->id, $db_data->msg, $db_data->act1, $db_data->label1, $db_data->value1, $db_data->act2, $db_data->label2, $db_data->value2];
					}
				}
				$scenario_options = FormParts::getMakeSelectOptions($list_scenario, "シナリオID：", $id);
			}
		}

		//ポストバックデータ取得
		$postback_data = Line_postback_template::get();
		if( !empty($postback_data) ){
			foreach($postback_data as $lines){
				$list_postback[] = [$lines->postback, $lines->name];
			}
			$postback_options = FormParts::getMakeSelectOptions($list_postback, '');
		}

		//画面表示用配列
		$disp_data = [
			'send_status'		=> $send_status,
			'push_title'		=> $push_title,
			'reserve_date'		=> $reserve_date,
			'channel_id'		=> $channel_id,
			'edit_id'			=> $id,
			'scenario_options'	=> $scenario_options,
			'postback_options'	=> $postback_options,
			'db_data'			=> $db_data,
			'list_msg'			=> $list_msg,
			'list_scenario'		=> $list_scenario,
			'list_postback'		=> $list_postback,
			'channel_id'		=> $channel_id,
			'redirect_url'		=> config('const.base_admin_url').'/member/line/setting/2choices/'.$channel_id,
			'save_redirect_url'	=> config('const.base_admin_url').'/member/line/setting/2choices/'.$channel_id,
			'ver'				=> time()
		];

		return view('admin.push.confirm', $disp_data);
	}

	public function index_4choices($channel_id, $id = "")
	{
		$disp_data			 = [];
		$db_data			 = [];
		$list_scenario		 = [];
		$list_postback		 = [];
		$list_msg			 = [];
		$push_title			 = '';
		$img_ratio			 = '';
		$img_size			 = '';
		$img				 = '';
		$reserve_date		 = '';
		$send_status		 = '';
		$scenario_options	 = '';
		$postback_options	 = '';

		if( !empty($id) ){
			//シナリオデータ取得
			$db_data = Line_4choices_template::select('line_4choices_templates.push_title', 'line_4choices_templates.send_status', 'line_4choices_templates.reserve_send_date', 'line_4choices_templates.reserve_send_date', 'line_4choices_templates.img_ratio', 'line_4choices_templates.img_size', 'line_4choices_templates.img', 'line_4choices_logs.*')->leftJoin('line_4choices_logs', 'line_4choices_templates.id', '=', 'line_4choices_logs.master_id')->where('line_4choices_templates.id', $id)->orderBy('line_4choices_logs.id')->get();
//error_log(print_r($db_data,true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

			if( !empty($db_data) ){
				foreach($db_data as $lines){
					$list_scenario[]	 = ['act=4ch&master_id='.$id.'&'.config('const.scenario_val_prefix').$lines->id, $lines->id];
					$send_status		 = $lines->send_status;
					$push_title			 = $lines->push_title;
					$reserve_date		 = $lines->reserve_send_date;
					$img_ratio			 = $lines->img_ratio;
					$img_size			 = $lines->img_size;
					$img				 = $lines->img;

					if( !empty($db_data->msg) ){
						$list_msg[] = [$db_data->id, $db_data->msg, $db_data->act1, $db_data->label1, $db_data->value1, $db_data->act2, $db_data->label2, $db_data->value2];
					}
				}
				$scenario_options = FormParts::getMakeSelectOptions($list_scenario, "シナリオID：", $id);
			}
		}

		//ポストバックデータ取得
		$postback_data = Line_postback_template::where('line_basic_id', $channel_id)->get();
		if( !empty($postback_data) ){
			foreach($postback_data as $lines){
				$list_postback[] = [$lines->postback, $lines->name];
			}
			$postback_options = FormParts::getMakeSelectOptions($list_postback, '');
		}

		//画面表示用配列
		$disp_data = [
			'send_status'		=> $send_status,
			'push_title'		=> $push_title,
			'reserve_date'		=> $reserve_date,
			'img_ratio'			=> $img_ratio,
			'img_size'			=> $img_size,
			'img'				=> $img,
			'channel_id'		=> $channel_id,
			'edit_id'			=> $id,
			'scenario_options'	=> $scenario_options,
			'postback_options'	=> $postback_options,
			'db_data'			=> $db_data,
			'list_msg'			=> $list_msg,
			'list_scenario'		=> $list_scenario,
			'list_postback'		=> $list_postback,
			'channel_id'		=> $channel_id,
			'redirect_url'		=> config('const.base_admin_url').'/member/line/setting/4choices/'.$channel_id,
			'save_redirect_url'	=> config('const.base_admin_url').'/member/line/setting/4choices/'.$channel_id,
			'ver'				=> time()
		];

		return view('admin.push.confirm_4choices', $disp_data);
	}

	/*
	 * 設定更新
	 */
	public function save2ConfirmSetting(Request $request, $channel_id){
		$db_data = [];
		$db_logs_data = [];
//error_log(print_r($request->all(),true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		$validate = [
			'reserve_date'	 => 'bail|required|date_format_check|surrogate_pair_check|emoji_check',
			'push_title'	 => 'bail|required|surrogate_pair_check',
		];

		if( !empty($request->input('text')) ){
			$validate['text'] = 'bail|required|surrogate_pair_check';
			$validate['label1'] = 'bail|required|surrogate_pair_check';
//			$validate['value1'] = 'bail|required|surrogate_pair_check';
			$validate['label2'] = 'bail|required|surrogate_pair_check';
//			$validate['value2'] = 'bail|required|surrogate_pair_check';
		}

			if( !empty($request->input('push_title')) ){
				$db_data['push_title'] = $request->input('push_title');
			}

			if( !empty($request->input('text')) ){
				$db_logs_data['msg'] = $request->input('text');
			}

			if( !empty($request->input('act1')) ){
				$db_logs_data['act1'] = $request->input('act1');
			}

			if( !empty($request->input('act2')) ){
				$db_logs_data['act2'] = $request->input('act2');
			}

			if( !empty($request->input('label1')) ){
				$db_logs_data['label1'] = $request->input('label1');
			}

			if( !empty($request->input('label2')) ){
				$db_logs_data['label2'] = $request->input('label2');
			}

			if( !empty($request->input('value1')) ){
				$db_logs_data['value1'] = $request->input('value1');
			}
			if( !empty($request->input('value2')) ){
				$db_logs_data['value2'] = $request->input('value2');
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

		$line_basic_id	 = $request->input('channel_id');
		$line_push_id	 = $request->input('edit_id');
		$send_type		 = $request->input('send_type');

		if( empty($line_push_id) ){
			$db_data = array_merge($db_data, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,	
				'send_status'			=> $request->input('send_status'),				//送信状況:0(配信待ち)
				'send_count'			=> 0,											//送信数
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			$line_push_id = Line_2choices_template::insertGetId($db_data);

			$db_logs_data = array_merge($db_logs_data, [
				'master_id'				=> $line_push_id,
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			Line_2choices_log::insert($db_logs_data);
		}else{
			$db_data = array_merge($db_data, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,								//予約配信:1
				'send_status'			=> $request->input('send_status'),			//送信状況:0(配信待ち)
				'send_count'			=> 0,										//送信数
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			Line_2choices_template::where('id', $line_push_id)->update($db_data);

			$db_logs_data = array_merge($db_logs_data, [
				'master_id'				=> $line_push_id,
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			if( !empty($request->input('logs_ids')) ){
				//メルマガログに送信情報を登録
				Line_2choices_log::where('id', $request->input('logs_ids'))->update($db_logs_data);
			}else{
				Line_2choices_log::insert($db_logs_data);
			}
		}

		return $line_push_id;
	}

	/*
	 * 設定更新
	 */
	public function save4ConfirmSetting(Request $request, $channel_id){
		$db_data = [];
		$db_logs_data = [];
//error_log(print_r($request->all(),true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		$validate = [
			'reserve_date'	 => 'bail|required|date_format_check|surrogate_pair_check|emoji_check',
			'push_title'	 => 'bail|required|surrogate_pair_check',
		];

		if( !empty($request->input('text')) ){
			$validate['text'] = 'bail|required|surrogate_pair_check';
//			$validate['label1'] = 'bail|required|surrogate_pair_check';
//			$validate['value1'] = 'bail|required|surrogate_pair_check';
//			$validate['label2'] = 'bail|required|surrogate_pair_check';
//			$validate['value2'] = 'bail|required|surrogate_pair_check';
//			$validate['label3'] = 'bail|required|surrogate_pair_check';
//			$validate['value3'] = 'bail|required|surrogate_pair_check';
//			$validate['label4'] = 'bail|required|surrogate_pair_check';
//			$validate['value4'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('push_title')) ){
			$db_data['push_title'] = $request->input('push_title');
		}

		if( !empty($request->input('img_ratio')) ){
			$db_data['img_ratio'] = $request->input('img_ratio');
		}

		if( !empty($request->input('img_size')) ){
			$db_data['img_size'] = $request->input('img_size');
		}

		if( !empty($request->input('text')) ){
			$db_logs_data['msg'] = $request->input('text');
		}

		if( !empty($request->input('act1')) ){
			$db_logs_data['act1'] = $request->input('act1');
		}

		if( !empty($request->input('act2')) ){
			$db_logs_data['act2'] = $request->input('act2');
		}

		if( !empty($request->input('act3')) ){
			$db_logs_data['act3'] = $request->input('act3');
		}

		if( !empty($request->input('act4')) ){
			$db_logs_data['act4'] = $request->input('act4');
		}

		if( !empty($request->input('label1')) ){
			$db_logs_data['label1'] = $request->input('label1');
		}

		if( !empty($request->input('label2')) ){
			$db_logs_data['label2'] = $request->input('label2');
		}

		if( !empty($request->input('label3')) ){
			$db_logs_data['label3'] = $request->input('label3');
		}

		if( !empty($request->input('label4')) ){
			$db_logs_data['label4'] = $request->input('label4');
		}

		if( !empty($request->input('value1')) ){
			$db_logs_data['value1'] = $request->input('value1');
		}

		if( !empty($request->input('value2')) ){
			$db_logs_data['value2'] = $request->input('value2');
		}

		if( !empty($request->input('value3')) ){
			$db_logs_data['value3'] = $request->input('value3');
		}

		if( !empty($request->input('value4')) ){
			$db_logs_data['value4'] = $request->input('value4');
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

		$line_basic_id	 = $request->input('channel_id');
		$line_push_id	 = $request->input('edit_id');
		$send_type		 = $request->input('send_type');

		if( empty($line_push_id) ){
			$db_data = array_merge($db_data, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,	
				'send_status'			=> $request->input('send_status'),				//送信状況:0(配信待ち)
				'send_count'			=> 0,											//送信数
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			$line_push_id = Line_4choices_template::insertGetId($db_data);

			$db_logs_data = array_merge($db_logs_data, [
				'master_id'				=> $line_push_id,
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			Line_4choices_log::insert($db_logs_data);
		}else{
			$db_data = array_merge($db_data, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,								//予約配信:1
				'send_status'			=> $request->input('send_status'),			//送信状況:0(配信待ち)
				'send_count'			=> 0,										//送信数
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			Line_4choices_template::where('id', $line_push_id)->update($db_data);

			$db_logs_data = array_merge($db_logs_data, [
				'master_id'				=> $line_push_id,
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			if( !empty($request->input('logs_ids')) ){
				//メルマガログに送信情報を登録
				Line_4choices_log::where('id', $request->input('logs_ids'))->update($db_logs_data);
			}else{
				Line_4choices_log::insert($db_logs_data);
			}
		}

		return $line_push_id;
	}

	/*
	 * 画像のアップロード処理
	 */
	public function uploadButtonImgUpload(Request $request, $channel_id)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//landing_pagesテーブルに登録されているidを取得
		$id					 = $request->input('edit_id');
		$img				 = $request->input('msg');
		$send_type			 = $request->input('send_type');
		$reserve_date		 = $request->input('tmp_reserve_date');
		$push_title			 = $request->input('tmp_push_title');
		$img_ratio			 = $request->input('tmp_img_ratio');
		$img_size			 = $request->input('tmp_img_size');

		$img_name			 = $file->getClientOriginalName();
		$now_date			 = Carbon::now();
//error_log("$channel_id, $file, $id, $img, $send_type, $now_date, $reserve_date, $push_title, $img_ratio, $img_size::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		try{
			$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $reserve_date.'00');
			$id = DB::transaction(function() use($channel_id, $file, $id, $img, $send_type, $reserve_date, $sort_reserve_date, $push_title, $img_ratio, $img_size, $now_date){
				//line_carousel_templatesテーブルにまだ登録されていないとき
				if( is_null($id) ){
					//最初の１回目の画像アップロードはinsert
					$id = Line_4choices_template::insertGetId([
						'line_basic_id'			=> $channel_id,
						'send_type'				=> $send_type,		//配信タイプ:
						'send_status'			=> 99,				//配信状況:99(送信前の保存)
						'send_count'			=> 0,
						'push_title'			=> $push_title,
						'img_ratio'				=> $img_ratio,
						'img_size'				=> $img_size,
						'reserve_send_date'		=> $reserve_date,
						'sort_reserve_send_date'=> $sort_reserve_date,
						'created_at'			=> $now_date,
						'updated_at'			=> $now_date
					]);
				}
//error_log("$channel_id, $file, $id, $img, ".$file->getClientOriginalExtension()."::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

				//画像名をid名にするためupdateを行う
				Line_4choices_template::where('id', $id)->update([
					'send_status'			=> 99,														//配信状況:99(送信前の保存)
					'push_title'			=> $push_title,
					'img_ratio'				=> $img_ratio,
					'img_size'				=> $img_size,
					'reserve_send_date'		=> $reserve_date,
					'sort_reserve_send_date'=> $sort_reserve_date,
					'img'					=> 'button_'.$id.'.'.$file->getClientOriginalExtension(),
				]);
//				throw new \Exception("テスト例外エラー");
				return $id;
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//画像の保存先を移動
		$file->move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img', 'button_'.$id.'.'.$file->getClientOriginalExtension());

		//画像を管理画面上のプレビュー用にコピー
		system('ln -s '.config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.'button_'.$id.'.'.$file->getClientOriginalExtension().' /data/www/line/public/images/preview/');

		//更新データを取得
		$db_data = Line_4choices_template::where('id',$id)->first();

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
	 * 画像のアップロード処理
	 */
	public function deleteButtonImgUpload(Request $request, $channel_id)
	{
		//landing_pagesテーブルに登録されているidを取得
		$id					 = $request->input('edit_id');
//error_log("$channel_id, $id::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//更新データを取得
		$db_data = Line_4choices_template::where('id',$id)->first();

		try{
			DB::transaction(function() use($id){
				//画像名をid名にするためupdateを行う
				Line_4choices_template::where('id', $id)->update([
					'img' => null,
				]);
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//プレビュー画像のシンボリックリンクを削除
		system('unlink /data/www/line/public/images/preview/'.$db_data->img);

		//画像削除
		system('rm -rf /data/www/line/public/images/preview/'.config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$db_data->img);
		system('rm -rf '.config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$db_data->img);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_upload'].",{$user['login_id']}");

		return config('const.base_admin_url').'/member/line/reserve/status/edit/1/7/'.$channel_id."/".$id;
	}
}
